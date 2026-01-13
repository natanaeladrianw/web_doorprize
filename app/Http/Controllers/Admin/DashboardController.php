<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Winner;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_peserta' => FormSubmission::count(),
            'total_pemenang' => Winner::count(),
            'peserta_baru' => FormSubmission::whereDate(
                'created_at',
                '>=',
                Carbon::now()->subDays(30)
            )->count(),
            'forms_aktif' => Form::where('is_active', true)->count(),
        ];

        $recent_activities = FormSubmission::with('form')
            ->latest()
            ->take(5)
            ->get();

        $recent_winners = Winner::with('submission.form')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_activities',
            'recent_winners'
        ));
    }
}
