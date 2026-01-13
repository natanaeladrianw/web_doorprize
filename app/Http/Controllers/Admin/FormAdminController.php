<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = Form::latest()->paginate(10);
        return view('admin.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Form::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => true,
            'created_by' => 1, // Replace with auth()->id() when auth is ready
        ]);

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        return view('admin.forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $form->update($request->only(['title', 'description', 'is_active']));

        return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }

    /**
     * Show the builder interface for the form.
     */
    public function builder(Form $form)
    {
        $form->load('fields');
        return view('admin.forms.builder', compact('form'));
    }

    /**
     * Preview form seperti tampilan user (untuk admin)
     */
    public function preview(Form $form)
    {
        $form->load('fields');
        return view('form.preview', compact('form'));
    }

    /**
     * Update the active status of the form.
     */
    public function updateStatus(Request $request, Form $form)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $form->update([
            'is_active' => $request->is_active
        ]);

        return back()->with('success', 'Form status updated successfully.');
    }
}
