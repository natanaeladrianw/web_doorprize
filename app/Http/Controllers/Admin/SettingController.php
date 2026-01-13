<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan
     */
    public function index(Request $request)
    {
        // Ambil semua form yang aktif dengan fields-nya
        $forms = Form::with('fields')->where('is_active', true)->get();

        // Form yang dipilih (dari query parameter atau default kosong)
        $selectedFormId = $request->query('form_id', '');

        // Ambil semua pengaturan per form
        $allSettings = [];
        foreach ($forms as $form) {
            $allSettings[$form->id] = Setting::get('raffle_display_fields_' . $form->id, []);
        }

        // Ambil separator global
        $raffleDisplaySeparator = Setting::get('raffle_display_separator', ' - ');

        return view('admin.settings.index', compact('forms', 'selectedFormId', 'allSettings', 'raffleDisplaySeparator'));
    }

    /**
     * Simpan pengaturan tampilan undian
     */
public function updateRaffleDisplay(Request $request)
{
    $request->validate([
        'form_id' => 'required|exists:forms,id',
        'display_fields' => 'nullable|array',
        'display_separator' => 'nullable|string|max:10',
    ]);

    $formId = $request->form_id;


    // Ambil data lamaa
    $oldFields = Setting::get('raffle_display_fields_' . $formId, []);
    $oldSeparator = Setting::get('raffle_display_separator', ' - ');


    // Ambil urutan field dari form
    $formFieldsOrder = Form::find($formId)
        ->fields()
        ->orderBy('id')
        ->pluck('label')
        ->toArray();


    // Field yang dipilih user
    $selectedFields = $request->display_fields ?? [];


    // Urutkan sesuai dengan field kolom
    $newFields = array_values(
        array_intersect($formFieldsOrder, $selectedFields)
    );

    $newSeparator = $request->display_separator ?? ' - ';



    // Cek tidak ada perubahan
    if ($oldFields === $newFields && $oldSeparator === $newSeparator) {
        return redirect()
            ->route('admin.settings.index', ['form_id' => $formId])
            ->with('info', 'Tidak ada perubahan yang disimpan.');
    }


    // simpan jika ada perubahan
    Setting::set('raffle_display_fields_' . $formId, $newFields);
    Setting::set('raffle_display_separator', $newSeparator);

    return redirect()
        ->route('admin.settings.index', ['form_id' => $formId])
        ->with('success', 'Pengaturan tampilan undian berhasil disimpan!');
}
    /**
     * Tampilkan halaman pengaturan header
     */
    public function headerSettings()
    {
        $headerImage = Setting::get('form_header_image');
        $raffleTitle = Setting::get('raffle_title', 'DOORPRIZE');
        $raffleSubtitle = Setting::get('raffle_subtitle', 'Pengundian Hadiah');
        
        return view('admin.settings.header', compact('headerImage', 'raffleTitle', 'raffleSubtitle'));
    }

    /**
     * Update header text settings
     */
    public function updateHeaderText(Request $request)
    {
        $request->validate([
            'raffle_title' => 'required|string|max:100',
            'raffle_subtitle' => 'nullable|string|max:200',
        ]);

        Setting::set('raffle_title', $request->raffle_title);
        Setting::set('raffle_subtitle', $request->raffle_subtitle);

        return redirect()->route('admin.settings.header')
            ->with('success', 'Teks header berhasil diperbarui!');
    }

    /**
     * Upload header image
     */
    public function uploadHeader(Request $request)
    {
        $request->validate([
            'header_image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($request->hasFile('header_image')) {
            $file = $request->file('header_image');
            $filename = 'form-header-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);

            // Hapus gambar lama jika ada
            $oldImage = Setting::get('form_header_image');
            if ($oldImage && file_exists(public_path($oldImage))) {
                unlink(public_path($oldImage));
            }

            Setting::set('form_header_image', 'uploads/' . $filename);

            return redirect()->route('admin.settings.header')
                ->with('success', 'Header image berhasil diupload!');
        }

        return redirect()->route('admin.settings.header')
            ->with('error', 'Gagal upload image!');
    }

    /**
     * Hapus header image
     */
    public function deleteHeader()
    {
        $headerImage = Setting::get('form_header_image');
        
        if ($headerImage && file_exists(public_path($headerImage))) {
            unlink(public_path($headerImage));
        }

        Setting::set('form_header_image', null);

        return redirect()->route('admin.settings.header')
            ->with('success', 'Header image berhasil dihapus!');
    }
}
