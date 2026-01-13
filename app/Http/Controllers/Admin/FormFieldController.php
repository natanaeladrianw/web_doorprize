<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormFieldController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Form $form)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'field_type' => 'required|in:text,radio,checkbox,dropdown',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'min_length' => 'nullable|integer|min:0',
            'max_length' => 'nullable|integer|min:1',
            'needs_validation' => 'boolean',
            'is_unique' => 'boolean',
        ]);

        // Build options array
        $options = $request->options ?? [];

        // Tambahkan constraint options untuk text field
        if ($request->field_type === 'text') {
            if ($request->filled('min_length')) {
                $options['min_length'] = (int) $request->min_length;
            }
            if ($request->filled('max_length')) {
                $options['max_length'] = (int) $request->max_length;
            }
            if ($request->boolean('needs_validation')) {
                $options['needs_validation'] = true;
            }
            if ($request->boolean('is_unique')) {
                $options['is_unique'] = true;
            }
        }

        $form->fields()->create([
            'label' => $request->label,
            'field_type' => $request->field_type,
            'options' => !empty($options) ? $options : null,
            'is_required' => $request->boolean('is_required'),
            'order' => $form->fields()->max('order') + 1,
        ]);

        return redirect()->route('admin.forms.builder', $form)->with('success', 'Field added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form, FormField $field)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'field_type' => 'required|in:text,radio,checkbox,dropdown',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'min_length' => 'nullable|integer|min:0',
            'max_length' => 'nullable|integer|min:1',
            'needs_validation' => 'boolean',
            'is_unique' => 'boolean',
        ]);

        // Build options array
        $options = $request->options ?? [];

        // Tambahkan constraint options untuk text field
        if ($request->field_type === 'text') {
            if ($request->filled('min_length')) {
                $options['min_length'] = (int) $request->min_length;
            }
            if ($request->filled('max_length')) {
                $options['max_length'] = (int) $request->max_length;
            }
            if ($request->boolean('needs_validation')) {
                $options['needs_validation'] = true;
            }
            if ($request->boolean('is_unique')) {
                $options['is_unique'] = true;
            }
        }

        $field->update([
            'label' => $request->label,
            'field_type' => $request->field_type,
            'options' => !empty($options) ? $options : null,
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('admin.forms.builder', $form)->with('success', 'Field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form, FormField $field)
    {
        $field->delete();
        return redirect()->route('admin.forms.builder', $form)->with('success', 'Field deleted.');
    }

    /**
     * Reorder fields via drag and drop
     */
    public function reorder(Request $request, Form $form)
    {
        $request->validate([
            'field_ids' => 'required|array',
            'field_ids.*' => 'required|integer|exists:form_fields,id',
        ]);

        foreach ($request->field_ids as $order => $fieldId) {
            FormField::where('id', $fieldId)
                ->where('form_id', $form->id)
                ->update(['order' => $order + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan field berhasil diperbarui.'
        ]);
    }
}
