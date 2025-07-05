<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayoutMethodRequest;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutMethodController extends Controller
{
    /**
     * Show payout methods setup page
     */
    public function index()
    {
        $applicant = Auth::user()->applicant;
        $payoutMethods = $applicant->payoutMethods()->get();

        return view('payout-methods.index', compact('payoutMethods'));
    }

    /**
     * Show create payout method form
     */
    public function create()
    {
        $banks = Bank::orderBy('display_name')->get();
        return view('payout-methods.create', compact('banks'));
    }

    /**
     * Store a new payout method
     */
    public function store(StorePayoutMethodRequest $request)
    {
        $applicant = Auth::user()->applicant;

        if (!$applicant) {
            return redirect()->back()
                ->with('error', 'No applicant profile found.');
        }

        $applicant->payoutMethods()->create([
            'type' => $request->type,
            'provider' => $request->provider,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_id' => $request->bank_id,
            'is_primary' => $request->boolean('is_primary'),
            'payable_type' => get_class($applicant),
            'payable_id' => $applicant->id,
        ]);

        return redirect()->back()
            ->with('success', 'Payout method added successfully!');
    }

    /**
     * Set primary payout method
     */
    public function setPrimary($id)
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return redirect()->back()->with('error', 'No applicant profile found.');
        }

        // Find the payout method for the applicant
        $payoutMethod = $applicant->payoutMethods()->findOrFail($id);

        // Unset any existing primary method for this applicant
        $applicant->payoutMethods()->where('is_primary', true)->update(['is_primary' => false]);

        // Set the selected method as primary
        $payoutMethod->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Primary payout method updated!');
    }

    /**
     * Delete payout method
     */
    public function destroy($id)
    {
        $applicant = Auth::user()->applicant;

        $payoutMethod = $applicant->payoutMethods()->findOrFail($id);
        $payoutMethod->delete();

        return redirect()->back()->with('success', 'Payout method deleted successfully!');
    }
}
