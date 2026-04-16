<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Models\Tenant;
use App\Models\TenantPlugin;
use Illuminate\Http\Request;

class AdminPluginController extends Controller
{
    public function index()
    {
        $plugins = Plugin::orderBy('sort_order')->get();
        $plans   = ['free', 'starter', 'pro', 'enterprise'];
        return view('admin.plugins.index', compact('plugins', 'plans'));
    }

    public function tenantPlugins(Tenant $tenant)
    {
        $pluginData = $tenant->pluginsForDisplay();
        $plans = ['free', 'starter', 'pro', 'enterprise'];
        return view('admin.plugins.tenant', compact('tenant', 'pluginData', 'plans'));
    }

    public function toggle(Request $request, Tenant $tenant, Plugin $plugin)
    {
        $action = $request->input('action');

        if ($action === 'reset') {
            TenantPlugin::where('tenant_id', $tenant->id)
                ->where('plugin_id', $plugin->id)
                ->delete();
        } else {
            $enabled = ($action === 'enable');
            $includedByPlan = $plugin->isIncludedInPlan($tenant->plan);
            $isOverride = ($enabled !== $includedByPlan);

            TenantPlugin::updateOrCreate(
                ['tenant_id' => $tenant->id, 'plugin_id' => $plugin->id],
                ['enabled' => $enabled, 'is_override' => $isOverride]
            );
        }

        $tenant->clearPluginCache();

        return redirect()->route('admin.plugins.tenant', $tenant)
            ->with('success', "Plugin '{$plugin->name}' updated for {$tenant->name}.");
    }

    public function bulkApplyPlan(Request $request, Tenant $tenant)
    {
        $newPlan = $request->validate(['plan' => 'required|in:free,starter,pro,enterprise'])['plan'];
        $tenant->update(['plan' => $newPlan]);
        TenantPlugin::where('tenant_id', $tenant->id)->where('is_override', false)->delete();
        $tenant->clearPluginCache();

        return redirect()->route('admin.plugins.tenant', $tenant)
            ->with('success', "Tenant plan changed to <strong>" . ucfirst($newPlan) . "</strong>. Plan-based plugins updated.");
    }

    public function update(Request $request, Plugin $plugin)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'min_plan'    => 'required|in:free,starter,pro,enterprise',
            'active'      => 'boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $plugin->update($data);

        foreach (Tenant::all() as $tenant) {
            $tenant->clearPluginCache();
        }

        return redirect()->route('admin.plugins.index')
            ->with('success', "Plugin '{$plugin->name}' updated.");
    }
}
