<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Referral settings
            'referral_cookie_days'            => ['required', 'integer', 'min:1', 'max:365'],
            'referral_attribution_model'      => ['required', 'in:last_click,first_click'],
            'referral_allow_self_referral'    => ['required', 'in:true,false,1,0'],
            'referral_code_prefix'            => ['nullable', 'string', 'max:10'],

            // Commission settings
            'commission_default_type'         => ['required', 'in:percent,fixed'],
            'commission_default_value'        => ['required', 'numeric', 'min:0'],
            'commission_hold_days'            => ['required', 'integer', 'min:0', 'max:90'],
            'commission_base'                 => ['required', 'in:product_subtotal,subtotal_before_tax,total'],

            // Withdrawal settings
            'withdrawal_min_amount'           => ['required', 'numeric', 'min:10'],
            'withdrawal_max_amount'           => ['required', 'numeric', 'gte:withdrawal_min_amount'],
            'withdrawal_fee_type'             => ['required', 'in:fixed,percent'],
            'withdrawal_fee_value'            => ['required', 'numeric', 'min:0'],
            'withdrawal_payout_days'          => ['nullable', 'string'],

            // Fraud settings
            'fraud_block_same_ip'             => ['required', 'in:true,false,1,0'],
            'fraud_max_clicks_per_minute'     => ['required', 'integer', 'min:1'],
        ]);

        $adminId = Auth::guard('admin')->id();

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) $value);
        }

        AuditLog::record(
            'settings_updated',
            null,
            [],
            $validated,
            'Platform referral and commission settings updated by Admin',
            $adminId,
            'Admin'
        );

        return back()->with('success', 'Referral, Commission & Withdrawal settings updated successfully.');
    }
}
