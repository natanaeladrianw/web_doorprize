<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormSubmissionController extends Controller
{
    /**
     * Tampilkan daftar form yang aktif
     */
    public function index()
    {
        $forms = Form::where('is_active', true)
            ->with('fields')
            ->withCount('submissions')
            ->latest()
            ->paginate(6);

        return view('submissions.index', compact('forms'));
    }

    /**
     * Tampilkan form untuk diisi
     */
    public function show(Request $request, Form $form)
    {
        // Pastikan form aktif
        if (!$form->is_active) {
            abort(404, 'Form tidak ditemukan atau sudah tidak aktif');
        }

        // Load fields dengan urutan
        $form->load('fields');

        // Simpan page parameter untuk redirect nanti
        $returnPage = $request->query('page', 1);

        return view('submissions.show', compact('form', 'returnPage'));
    }

    /**
     * Simpan submission form
     */
    public function store(Request $request, Form $form)
    {
        // Pastikan form aktif
        if (!$form->is_active) {
            return back()->with('error', 'Form sudah tidak aktif');
        }

        // Load fields untuk validation
        $form->load('fields');

        // ============================================
        // VALIDASI UNIK INDIVIDUAL (is_unique)
        // Cek field yang harus memiliki nilai unik
        // ============================================
        foreach ($form->fields as $field) {
            $options = $field->options ?? [];

            // Cek apakah field ini harus unik
            if (isset($options['is_unique']) && $options['is_unique']) {
                $fieldKey = 'field_' . $field->id;
                $inputValue = $request->input($fieldKey);

                if ($inputValue) {
                    // Cari apakah nilai ini sudah ada di submission lain
                    $existingSubmission = FormSubmission::where('form_id', $form->id)
                        ->get()
                        ->first(function ($submission) use ($field, $inputValue) {
                            $data = $submission->submission_data ?? [];

                            // Cek di submission_data (format key-value)
                            if (isset($data[$field->label]) && $data[$field->label] == $inputValue) {
                                return true;
                            }

                            // Fallback: cek di format array lama
                            if (is_array($data)) {
                                foreach ($data as $key => $value) {
                                    if ((stripos($key, $field->label) !== false || $key === $field->label) && $value == $inputValue) {
                                        return true;
                                    }
                                }
                            }

                            return false;
                        });

                    if ($existingSubmission) {
                        return back()
                            ->withInput()
                            ->with('error', $field->label . ' "' . $inputValue . '" sudah terdaftar. Nilai ' . $field->label . ' harus berbeda.');
                    }
                }
            }
        }

        // ============================================
        // VALIDASI KOMBINASI (needs_validation)
        // Cek kombinasi field yang harus sama untuk ditolak
        // ============================================
        $validationFields = [];
        $validationValues = [];
        $validationLabels = [];

        foreach ($form->fields as $field) {
            $options = $field->options ?? [];

            // Cek apakah field ini perlu validasi kombinasi
            if (isset($options['needs_validation']) && $options['needs_validation']) {
                $fieldKey = 'field_' . $field->id;
                $inputValue = $request->input($fieldKey);

                if ($inputValue) {
                    $validationFields[] = $field;
                    $validationValues[$field->label] = $inputValue;
                    $validationLabels[] = $field->label;
                }
            }
        }

        // Jika ada field yang perlu divalidasi, cek duplikat kombinasi
        if (!empty($validationFields)) {
            $existingSubmission = FormSubmission::where('form_id', $form->id)
                ->get()
                ->first(function ($submission) use ($validationValues) {
                    $data = $submission->submission_data ?? [];

                    // Cek apakah SEMUA field validasi nilainya sama
                    $allMatch = true;
                    foreach ($validationValues as $label => $expectedValue) {
                        $found = false;

                        // Cek di submission_data (format key-value)
                        if (isset($data[$label]) && $data[$label] == $expectedValue) {
                            $found = true;
                        }

                        // Fallback: cek di format array lama
                        if (!$found && is_array($data)) {
                            foreach ($data as $key => $value) {
                                if (stripos($key, $label) !== false || $key === $label) {
                                    if ($value == $expectedValue) {
                                        $found = true;
                                        break;
                                    }
                                }
                            }
                        }

                        if (!$found) {
                            $allMatch = false;
                            break;
                        }
                    }

                    return $allMatch;
                });

            if ($existingSubmission) {
                $fieldNames = implode(' dan ', $validationLabels);
                $fieldValues = implode(', ', array_map(function ($label, $value) {
                    return "$label: \"$value\"";
                }, array_keys($validationValues), $validationValues));

                return back()
                    ->withInput()
                    ->with('error', 'Data dengan ' . $fieldValues . ' sudah terdaftar. Anda hanya dapat mengisi form ini satu kali.');
            }
        }

        // Build validation rules
        $rules = [];
        $messages = [];

        foreach ($form->fields as $field) {
            $fieldKey = 'field_' . $field->id;
            $fieldRules = [];
            $options = $field->options ?? [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
                $messages[$fieldKey . '.required'] = $field->label . ' wajib diisi';
            }

            // Validasi spesifik berdasarkan tipe field
            if ($field->field_type === 'checkbox' && $field->is_required) {
                $fieldRules = ['required', 'array'];
            }

            // Validasi min/max length untuk text field
            if ($field->field_type === 'text') {
                if (isset($options['min_length']) && $options['min_length'] > 0) {
                    $fieldRules[] = 'min:' . $options['min_length'];
                    $messages[$fieldKey . '.min'] = $field->label . ' minimal ' . $options['min_length'] . ' karakter';
                }
                if (isset($options['max_length']) && $options['max_length'] > 0) {
                    $fieldRules[] = 'max:' . $options['max_length'];
                    $messages[$fieldKey . '.max'] = $field->label . ' maksimal ' . $options['max_length'] . ' karakter';
                }
            }

            if (!empty($fieldRules)) {
                $rules[$fieldKey] = $fieldRules;
            }
        }

        // Validate
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Build submission data dengan format key-value untuk kemudahan pencarian
        $submissionData = [];
        $keyValueData = [];

        foreach ($form->fields as $field) {
            $fieldKey = 'field_' . $field->id;
            $value = $request->input($fieldKey);

            $submissionData[] = [
                'field_id' => $field->id,
                'label' => $field->label,
                'field_type' => $field->field_type,
                'value' => $value
            ];

            // Simpan juga sebagai key-value untuk kemudahan akses
            $keyValueData[$field->label] = $value;
        }

        // Simpan submission
        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'submission_data' => $keyValueData, // Gunakan format key-value
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Ambil page parameter untuk redirect
        $returnPage = $request->input('return_page', 1);

        // Redirect dengan pesan sukses ke halaman yang sama
        return redirect()
            ->route('forms.index', ['page' => $returnPage])
            ->with('success', 'Terima kasih! Form berhasil dikirim. Semoga beruntung! 🎉');
    }
}
