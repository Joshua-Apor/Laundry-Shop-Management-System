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

        $hasReportInput = $request->hasAny(['period', 'date', 'start_date', 'end_date', 'month', 'year']);
        $validator = Validator::make($request->query(), [
            'period' => [$hasReportInput ? 'required' : 'nullable', 'string', Rule::in(['Daily', 'Weekly', 'Monthly', 'Custom'])],
            'date' => ['required_if:period,Daily', 'nullable', 'date_format:Y-m-d'],
            'start_date' => ['required_if:period,Weekly,Custom', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:period,Custom', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'month' => ['required_if:period,Monthly', 'nullable', 'integer', 'between:1,12'],
            'year' => ['required_if:period,Monthly', 'nullable', 'integer', 'digits:4', 'between:1900,2100'],
        ]);
        $filters = $validator->passes() ? $validator->validated() : [];
        $period = $filters['period'] ?? 'Daily';
        $date = match ($period) {
            'Weekly', 'Custom' => $filters['start_date'] ?? now()->toDateString(),
            'Monthly' => sprintf('%04d-%02d-01', $filters['year'] ?? now()->year, $filters['month'] ?? now()->month),
            default => $filters['date'] ?? now()->toDateString(),
        };
        $endDate = $filters['end_date'] ?? null;
        $report = $hasReportInput && $validator->passes()
            ? $salesReportService->generate($period, $date, $endDate)
            : null;
        $exportFilters = match ($period) {
            'Weekly' => ['period' => $period, 'start_date' => $date],
            'Monthly' => ['period' => $period, 'month' => $filters['month'] ?? now()->month, 'year' => $filters['year'] ?? now()->year],
            'Custom' => ['period' => $period, 'start_date' => $date, 'end_date' => $endDate],
            default => ['period' => 'Daily', 'date' => $date],
        };

        return view('manager.sales-reports', [
            'period' => $period,
            'date' => $date,
            'month' => $filters['month'] ?? now()->month,
            'year' => $filters['year'] ?? now()->year,
            'startDate' => $filters['start_date'] ?? $date,
            'endDate' => $filters['end_date'] ?? null,
            'exportFilters' => $exportFilters,
            'report' => $report,
        ])->withErrors($validator);
    }

    public function export(Request $request, string $format, SalesReportService $salesReportService, SalesReportExporter $salesReportExporter): StreamedResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);
        abort_unless(in_array($format, ['pdf', 'docx', 'xlsx'], true), 404);

        $validator = Validator::make($request->query(), [
            'period' => ['required', 'string', Rule::in(['Daily', 'Weekly', 'Monthly', 'Custom'])],
            'date' => ['required_if:period,Daily', 'nullable', 'date_format:Y-m-d'],
            'start_date' => ['required_if:period,Weekly,Custom', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:period,Custom', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'month' => ['required_if:period,Monthly', 'nullable', 'integer', 'between:1,12'],
            'year' => ['required_if:period,Monthly', 'nullable', 'integer', 'digits:4', 'between:1900,2100'],
        ]);
        abort_if($validator->fails(), 422);
        $validated = $validator->validated();

        $date = match ($validated['period']) {
            'Weekly', 'Custom' => $validated['start_date'],
            'Monthly' => sprintf('%04d-%02d-01', $validated['year'], $validated['month']),
            default => $validated['date'],
        };
        $report = $salesReportService->generate($validated['period'], $date, $validated['end_date'] ?? null);
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
        $periodSuffix = $validated['period'] === 'Custom'
            ? $validated['start_date'].'-to-'.$validated['end_date']
            : $date;
        $filename = 'sales-report-'.strtolower($validated['period']).'-'.$periodSuffix.'.'.$format;

        return response()->streamDownload(function () use ($salesReportExporter, $report, $exportMethod): void {
            echo $salesReportExporter->{$exportMethod}($report);
        }, $filename, ['Content-Type' => $contentType]);
    }
}
