<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WinnerController extends Controller
{
    // Manual winner
    public function manual(Request $request, Form $form, FormSubmission $formSubmission)
    {
        $request->validate([
            'prize_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $form, $formSubmission) {
            // Ensure submission belongs to this form
            if ($formSubmission->form_id !== $form->id) {
                abort(403);
            }

            // Prevent duplicate winner
            $exists = Winner::where('form_submission_id', $formSubmission->id)->exists();

            if ($exists) {
                abort(409, 'This submission already has a winner.');
            }

            Winner::create([
                'form_submission_id' => $formSubmission->id,
                'prize_name' => $request->prize_name,
                'selection_method' => 'manual',
                'selected_by' => auth()->id(),
                'selected_at' => now(),
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Winner selected successfully.');
    }

    public function automatic(Request $request, Form $form)
    {
        $request->validate([
            'prize_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $form) {

            // Semua peserta bisa menang berkali-kali
            $submission = FormSubmission::where('form_id', $form->id)
                ->inRandomOrder()
                ->first();

            if (!$submission) {
                abort(409, 'No eligible submissions left.');
            }

            Winner::create([
                'form_submission_id' => $submission->id,
                'prize_name' => $request->prize_name,
                'selection_method' => 'automatic',
                'selected_by' => auth()->id() ?? 1,
                'selected_at' => now(),
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Winner selected successfully.');
    }
}
