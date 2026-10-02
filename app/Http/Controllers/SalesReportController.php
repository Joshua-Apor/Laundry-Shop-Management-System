<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExporter;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportController extends Controller
{
    public function index(Request $request, SalesReportService $salesReportService): View
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $hasReportInput = $request->hasAny(['period', 'date']);
        $validator = Validator::make($request->query(), [
            'period' => [$hasReportInput ? 'required' : 'nullable', 'string', Rule::in(['Daily', 'Weekly', 'Monthly'])],
            'date' => [$hasReportInput ? 'required' : 'nullable', 'date_format:Y-m-d'],
        ]);
        $filters = $validator->passes() ? $validator->validated() : [];
        $period = $filters['period'] ?? 'Daily';
        $date = $filters['date'] ?? now()->toDateString();
        $report = $hasReportInput && $validator->passes()
            ? $salesReportService->generate($period, $date)
            : null;

        return view('manager.sales-reports', [
            'period' => $period,
            'date' => $date,
            'report' => $report,
        ])->withErrors($validator);
    }

    public function export(Request $request, string $format, SalesReportService $salesReportService, SalesReportExporter $salesReportExporter): StreamedResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);
        abort_unless(in_array($format, ['pdf', 'docx', 'xlsx'], true), 404);

        $validator = Validator::make($request->query(), [
            'period' => ['required', 'string', Rule::in(['Daily', 'Weekly', 'Monthly'])],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);
        abort_if($validator->fails(), 422);
        $validated = $validator->validated();

        $report = $salesReportService->generate($validated['period'], $validated['date']);
        $exportMethod = match ($format) {
            'pdf' => 'toPdf',
            'docx' => 'toDocx',
            'xlsx' => 'toExcel',
        };
        $contentType = match ($format) {
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
        $filename = 'sales-report-'.strtolower($validated['period']).'-'.$validated['date'].'.'.$format;

        return response()->streamDownload(function () use ($salesReportExporter, $report, $exportMethod): void {
            echo $salesReportExporter->{$exportMethod}($report);
        }, $filename, ['Content-Type' => $contentType]);
    }
}
