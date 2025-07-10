<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayoutMethodRequest;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PayoutMethodController extends Controller
{
    /**
     * Show payout methods setup page
     */
    public function index()
    {
        $user = Auth::user();
        
        // NEW APPROACH: Get payout methods directly from the user
        $payoutMethods = $user->payoutMethods()
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // // Optional: Group by type for better organization
        // $payoutMethodsByType = $payoutMethods->groupBy('type');
        
        // // Optional: Get some statistics
        // $stats = [
        //     'total_methods' => $payoutMethods->count(),
        //     'verified_methods' => $payoutMethods->where('is_verified', true)->count(),
        //     'primary_method' => $payoutMethods->where('is_primary', true)->first(),
        //     'has_paybill' => $payoutMethods->where('type', 'paybill')->count() > 0,
        //     'has_mobile_money' => $payoutMethods->where('type', 'mobile_money')->count() > 0,
        //     'has_bank_account' => $payoutMethods->where('type', 'bank_account')->count() > 0,
        // ];

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
        $user = Auth::user();
        
        // Prepare data for user-linked payout method
        $data = [
            'user_id' => $user->id,
            'type' => $request->type,
            'account_number' => $request->account_number,
            'is_primary' => $request->boolean('is_primary'),
        ];
    
        // Add type-specific fields
        if ($request->type === 'mobile_money') {
            $data['provider'] = $request->provider;
            $data['account_name'] = $request->account_name;
        } elseif ($request->type === 'bank_account') {
            $data['bank_id'] = $request->bank_id;
            $data['account_name'] = $request->account_name;
        } elseif ($request->type === 'paybill') {
            $data['provider'] = $request->provider;
            $data['paybill_number'] = $request->paybill_number;
            $data['paybill_account_name'] = $request->paybill_account_name;
            $data['account_name'] = $request->paybill_account_name;
            
            if ($request->filled('paybill_description')) {
                $data['paybill_settings'] = json_encode([
                    'description' => $request->paybill_description,
                    'created_at' => now(),
                ]);
            }
        }
    
        try {
            $payoutMethod = $user->payoutMethods()->create($data);
    
            return redirect()->back()
                ->with('success', 'Payout method added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add payout method for applicant', [                
                'payout_type' => $request->type,
                'request_data' => array_diff_key($request->all(), array_flip(['account_number', 'paybill_account_name'])),
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add payout method. Please try again.')
                ->withInput();
        }
    }

    

    /**
     * Set primary payout method
     */
    public function setPrimary($id)
    {
        $user = Auth::user();
        
        // Find the payout method for the user
        $payoutMethod = $user->payoutMethods()->findOrFail($id);
        
        // Unset any existing primary method for this user
        $user->payoutMethods()->where('is_primary', true)->update(['is_primary' => false]);
        
        // Set the selected method as primary
        $payoutMethod->update(['is_primary' => true]);
        
        return redirect()->back()->with('success', 'Primary payout method updated!');
    }

    /**
     * Delete payout method
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $payoutMethod = $user->payoutMethods()->findOrFail($id);
        $payoutMethod->delete();
        
        return redirect()->back()->with('success', 'Payout method deleted successfully!');
    }
}
