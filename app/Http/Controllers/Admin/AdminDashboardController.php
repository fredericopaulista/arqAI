<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_tenants' => Tenant::count(),
                'total_users' => User::count(),
                'total_projects' => Project::count(),
                'total_credits' => User::sum('credits'),
                'avg_generation_time' => round(Project::whereNotNull('generation_completed_at')
                    ->whereNotNull('generation_started_at')
                    ->get()
                    ->avg(function ($p) {
                        return $p->generation_completed_at->diffInSeconds($p->generation_started_at);
                    }) ?? 0, 1)
            ],
            'recent_projects' => Project::with(['user', 'tenant'])->orderBy('created_at', 'desc')->limit(10)->get()
        ]);
    }

    public function tenants()
    {
        return Inertia::render('Admin/Tenants/Index', [
            'tenants' => Tenant::withCount(['users', 'projects'])->get()
        ]);
    }

    public function projects()
    {
        return Inertia::render('Admin/Projects/Index', [
            'projects' => Project::with(['user', 'tenant'])->orderBy('created_at', 'desc')->paginate(20)
        ]);
    }

    public function settings()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => \App\Models\Setting::all()
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($data['settings'] as $item) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => trim($item['value'] ?? '')]
            );
        }

        return back()->with('success', 'Configurações atualizadas.');
    }

    public function adjustCredits(Request $request, User $user)
    {
        $request->validate([
            'credits' => 'required|integer',
        ]);

        $user->update(['credits' => $request->credits]);

        return back()->with('success', 'Créditos atualizados.');
    }
}
