<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $seller = Auth::user()->seller;
        return view('seller.settings', compact('seller'));
    }

    public function update(Request $request)
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'store_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sellers,slug,' . $seller->id,
            'bio' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
            'payout_email' => 'nullable|email',
            'payout_method' => 'nullable|in:paypal,bank,stripe_connect',
            'payout_details' => 'nullable|array',
        ]);

        $data = $request->only(['store_name', 'slug', 'bio', 'website', 'payout_email', 'payout_method', 'payout_details']);

        if ($request->hasFile('logo')) {
            if ($seller->logo_path) {
                Storage::disk('public')->delete($seller->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('seller/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($seller->banner_path) {
                Storage::disk('public')->delete($seller->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('seller/banners', 'public');
        }

        $seller->update($data);

        return back()->with('success', 'Store settings updated');
    }
}