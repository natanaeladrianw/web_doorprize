<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Prize;
use App\Models\FormSubmission;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrizeController extends Controller
{
    /**
     * Simpan hadiah baru
     */
    public function store(Request $request, Form $form)
    {
        $request->validate([
            'category_id' => 'required|exists:prize_categories,id',
            'name' => 'required|string|max:255',
            'quantity' => 'integer|min:1',
        ]);

        $maxOrder = $form->prizes()->max('order') ?? 0;

        $form->prizes()->create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'quantity' => $request->quantity ?? 1,
            'order' => $maxOrder + 1,
        ]);

        return back()->with([
            'success' => 'Hadiah berhasil ditambahkan!',
            'selected_form_id' => $form->id
        ]);
    }

    /**
     * Update hadiah
     */
    public function update(Request $request, Prize $prize)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $prize->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with([
            'success' => 'Hadiah berhasil diupdate!',
            'selected_form_id' => $prize->form_id
        ]);
    }

    /**
     * Hapus hadiah
     */
    public function destroy(Prize $prize)
    {
        $formId = $prize->form_id;

        // Hapus winner yang terkait jika ada
        if ($prize->winner) {
            $prize->winner->delete();
        }

        $prize->delete();

        return back()->with([
            'success' => 'Hadiah berhasil dihapus!',
            'selected_form_id' => $formId
        ]);
    }

    /**
     * Set preset pemenang manual (dari halaman pemenang)
     * Hanya menyimpan preset, belum ke tabel winners
     */
    public function selectWinnerManual(Request $request, Prize $prize)
    {
        $request->validate([
            'submission_id' => 'required|exists:form_submissions,id',
        ]);

        // Cek apakah hadiah sudah ada pemenangnya (tersimpan di winners)
        if ($prize->hasWinner()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Hadiah ini sudah memiliki pemenang!'], 400);
            }
            return back()->with([
                'error' => 'Hadiah ini sudah memiliki pemenang!',
                'selected_form_id' => $prize->form_id
            ]);
        }

        $submission = FormSubmission::findOrFail($request->submission_id);
        $submissionData = $submission->submission_data;
        $winnerName = is_array($submissionData)
            ? ($submissionData['Nama Lengkap'] ?? $submissionData['nama'] ?? 'Peserta #' . $submission->id)
            : 'Peserta #' . $submission->id;

        // Jika request dari AJAX (halaman undian) - simpan langsung ke winners
        if ($request->expectsJson()) {
            // Gunakan selection_method dari request (manual atau random)
            $selectionMethod = $request->input('selection_method', 'random');

            // Buat winner record
            Winner::create([
                'form_submission_id' => $request->submission_id,
                'prize_id' => $prize->id,
                'prize_name' => $prize->name,
                'selection_method' => $selectionMethod,
                'selected_by' => Auth::id(),
                'selected_at' => now(),
            ]);

            // Hapus preset jika ada
            $prize->update(['preset_submission_id' => null]);

            $winCount = $submission->winners()->count();
            $message = 'Pemenang "' . $winnerName . '" berhasil disimpan untuk hadiah "' . $prize->name . '"!';
            if ($winCount > 1) {
                $message .= ' (Peserta ini sudah menang ' . $winCount . ' kali)';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'winner' => [
                    'id' => $submission->id,
                    'name' => $winnerName,
                    'prize_id' => $prize->id,
                    'prize_name' => $prize->name,
                ]
            ]);
        }

        // Jika request dari halaman pemenang - hanya set preset
        $prize->update(['preset_submission_id' => $request->submission_id]);

        $message = 'Preset pemenang "' . $winnerName . '" berhasil diatur untuk hadiah "' . $prize->name . '"!';
        $message .= ' Otomatis ke simpan saat stop undian.';

        return back()->with([
            'success' => $message,
            'selected_form_id' => $prize->form_id
        ]);
    }

    /**
     * Set preset pemenang secara acak (dari halaman pemenang)
     * Hanya menyimpan preset, belum ke tabel winners
     */
    public function selectWinnerRandom(Prize $prize)
    {
        // Cek apakah hadiah sudah ada pemenangnya (tersimpan di winners)
        if ($prize->hasWinner()) {
            return back()->with([
                'error' => 'Hadiah ini sudah memiliki pemenang!',
                'selected_form_id' => $prize->form_id
            ]);
        }

        // Ambil peserta secara acak
        $randomSubmission = FormSubmission::where('form_id', $prize->form_id)
            ->inRandomOrder()
            ->first();

        if (!$randomSubmission) {
            return back()->with([
                'error' => 'Tidak ada peserta yang tersedia untuk diundi!',
                'selected_form_id' => $prize->form_id
            ]);
        }

        // Hanya set preset, belum simpan ke winners
        $prize->update(['preset_submission_id' => $randomSubmission->id]);

        $submissionData = $randomSubmission->submission_data;
        $winnerName = is_array($submissionData)
            ? ($submissionData['Nama Lengkap'] ?? $submissionData['nama'] ?? 'Peserta #' . $randomSubmission->id)
            : 'Peserta #' . $randomSubmission->id;

        $message = 'Preset pemenang acak "' . $winnerName . '" berhasil diatur untuk hadiah "' . $prize->name . '"!';
        $message .= ' Otomatis ke simpan saat stop undian.';

        return back()->with([
            'success' => $message,
            'selected_form_id' => $prize->form_id
        ]);
    }

    /**
     * Batalkan pemenang/preset (reset hadiah)
     */
    public function resetWinner(Prize $prize)
    {
        $hasChanges = false;
        $message = '';

        // Hapus winner jika ada
        if ($prize->winner) {
            $prize->winner->delete();
            $hasChanges = true;
            $message = 'Pemenang untuk hadiah "' . $prize->name . '" berhasil direset!';
        }

        // Hapus preset jika ada
        if ($prize->preset_submission_id) {
            $prize->update(['preset_submission_id' => null]);
            $hasChanges = true;
            if (empty($message)) {
                $message = 'Preset pemenang untuk hadiah "' . $prize->name . '" berhasil dihapus!';
            }
        }

        if ($hasChanges) {
            return back()->with([
                'success' => $message,
                'selected_form_id' => $prize->form_id
            ]);
        }

        return back()->with([
            'error' => 'Hadiah ini belum memiliki pemenang atau preset!',
            'selected_form_id' => $prize->form_id
        ]);
    }
}
