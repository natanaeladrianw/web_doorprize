<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\PrizeCategory;
use Illuminate\Http\Request;

class PrizeCategoryController extends Controller
{
    /**
     * Simpan kategori hadiah baru
     */
    public function store(Request $request, Form $form)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxOrder = $form->prizeCategories()->max('order') ?? 0;

        $form->prizeCategories()->create([
            'name' => $request->name,
            'order' => $maxOrder + 1,
        ]);

        return back()->with([
            'success' => 'Kategori hadiah berhasil ditambahkan!',
            'selected_form_id' => $form->id
        ]);
    }

    /**
     * Update kategori hadiah
     */
    public function update(Request $request, PrizeCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return back()->with([
            'success' => 'Kategori hadiah berhasil diupdate!',
            'selected_form_id' => $category->form_id
        ]);
    }

    /**
     * Hapus kategori hadiah
     */
    public function destroy(PrizeCategory $category)
    {
        $formId = $category->form_id;

        // Cek apakah masih ada hadiah yang menggunakan kategori ini
        if ($category->prizes()->count() > 0) {
            return back()->with([
                'error' => 'Kategori tidak dapat dihapus karena masih ada hadiah yang menggunakannya!',
                'selected_form_id' => $formId
            ]);
        }

        $category->delete();

        return back()->with([
            'success' => 'Kategori hadiah berhasil dihapus!',
            'selected_form_id' => $formId
        ]);
    }
}
