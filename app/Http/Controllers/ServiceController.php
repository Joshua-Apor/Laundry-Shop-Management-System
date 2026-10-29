<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $services = DB::table('services')
            ->whereNull('deleted_at')
            ->orderBy('service_name')
            ->get(['service_id', 'service_name', 'description', 'base_price', 'price_unit', 'available_for']);

        return view('manager.services', ['services' => $services]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255', Rule::unique('services', 'service_name')->whereNull('deleted_at')],
            'base_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_unit' => ['required', 'string', 'max:30'],
            'available_for' => ['required', Rule::in(['Drop Off', 'Self Service', 'Both'])],
        ]);

        DB::table('services')->insert([
            'service_name' => $validated['service_name'],
            'description' => null,
            'base_price' => $validated['base_price'],
            'price_unit' => $validated['price_unit'],
            'available_for' => $validated['available_for'],
        ]);

        return redirect()->route('manager.services')->with('success', 'Service added.');
    }

    public function destroy(Request $request, int $service): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $serviceRecord = DB::table('services')
            ->where('service_id', $service)
            ->whereNull('deleted_at')
            ->first(['service_id', 'service_name']);

        abort_if($serviceRecord === null, 404);
        abort_if(in_array($serviceRecord->service_name, ['Drop Off', 'Self Service'], true), 403);
        $expectedName = trim($serviceRecord->service_name);

        $request->merge(['confirmation_name' => $request->string('confirmation_name')->trim()->toString()]);
        $request->validate([
            'confirmation_name' => ['required', 'string', Rule::in([$expectedName])],
        ]);

        DB::table('services')
            ->where('service_id', $serviceRecord->service_id)
            ->update(['deleted_at' => now()]);

        return redirect()->route('manager.services')->with('success', 'Service deleted.');
    }
}
