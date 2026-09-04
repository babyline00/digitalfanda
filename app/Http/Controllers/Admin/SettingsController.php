<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $groups = Setting::distinct('group')->pluck('group');
        $activeGroup = $request->get('group', 'general');

        $settings = Setting::where('group', $activeGroup)->orderBy('key')->get();

        return view('admin.settings.index', compact('groups', 'activeGroup', 'settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($request->settings as $id => $data) {
            $setting = Setting::find($id);
            if ($setting) {
                $setting->update(['value' => $data['value']]);
            }
        }

        return back()->with('success', 'Settings updated');
    }
}