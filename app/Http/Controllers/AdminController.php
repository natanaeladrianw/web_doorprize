<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Models\Winner;
use App\Models\Form;
use App\Models\Prize;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_peserta' => FormSubmission::count(),
            'total_pemenang' => Winner::count(),
            'peserta_baru' => FormSubmission::whereDate('created_at', '>=', now()->subDays(30))->count(),
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

        return view('admin.dashboard', compact('stats', 'recent_activities', 'recent_winners'));
    }

    public function undian()
    {
        $forms = Form::where('is_active', true)->orderBy('title')->get();
        return view('admin.undian', compact('forms'));
    }

    public function getFormCandidates(Form $form)
    {
        // Ambil pengaturan tampilan per form
        $displayFields = Setting::get('raffle_display_fields_' . $form->id, []);
        $separator = Setting::get('raffle_display_separator', ' - ');

        // Get all candidates (semua peserta bisa menang berkali-kali)
        $allCandidates = $form->submissions()
            ->withCount('winners')
            ->select('id', 'submission_data')
            ->get()
            ->map(function ($submission) use ($displayFields, $separator) {
                $data = $submission->submission_data;

                // Build display name based on settings
                if (!empty($displayFields) && is_array($data)) {
                    $displayParts = [];
                    foreach ($displayFields as $fieldLabel) {
                        // Cari nilai field berdasarkan label (case-insensitive)
                        foreach ($data as $key => $value) {
                            if (stripos($key, $fieldLabel) !== false || $key === $fieldLabel) {
                                if ($value) {
                                    $displayParts[] = $value;
                                }
                                break;
                            }
                        }
                    }
                    $name = !empty($displayParts) ? implode($separator, $displayParts) : 'Peserta #' . $submission->id;
                } else {
                    // Fallback ke nama lama
                    $name = is_array($data) ? ($data['Nama Lengkap'] ?? $data['nama'] ?? 'Peserta #' . $submission->id) : 'Peserta #' . $submission->id;
                }

                return [
                    'id' => $submission->id,
                    'name' => $name,
                    'win_count' => $submission->winners_count ?? 0,
                    'masked_name' => str_pad(substr($name, 0, 3), strlen($name), '*')
                ];
            });

        // Get ALL prizes (termasuk yang sudah ada pemenangnya)
        $allPrizes = $form->prizes()
            ->with(['presetSubmission', 'winner.submission', 'category'])
            ->get()
            ->map(function ($prize) {
                $prizeData = [
                    'id' => $prize->id,
                    'name' => $prize->name,
                    'description' => $prize->description,
                    'category' => $prize->category?->name,
                    'has_preset_winner' => false,
                    'preset_winner' => null,
                    'is_saved' => false,
                    'saved_winner' => null,
                ];

                // Jika sudah ada winner yang tersimpan di tabel winners
                if ($prize->winner) {
                    $winnerSubmission = $prize->winner->submission;
                    $winnerData = $winnerSubmission->submission_data;
                    $winnerName = is_array($winnerData)
                        ? ($winnerData['Nama Lengkap'] ?? $winnerData['nama'] ?? 'Peserta #' . $winnerSubmission->id)
                        : 'Peserta #' . $winnerSubmission->id;

                    $prizeData['is_saved'] = true;
                    $prizeData['saved_winner'] = [
                        'id' => $winnerSubmission->id,
                        'name' => $winnerName,
                        'selected_at' => $prize->winner->selected_at->format('d M Y, H:i'),
                        'selection_method' => $prize->winner->selection_method,
                    ];
                }
                // Jika ada preset winner (dipilih tapi belum disimpan)
                elseif ($prize->presetSubmission) {
                    $presetSubmission = $prize->presetSubmission;
                    $presetData = $presetSubmission->submission_data;
                    $presetName = is_array($presetData)
                        ? ($presetData['Nama Lengkap'] ?? $presetData['nama'] ?? 'Peserta #' . $presetSubmission->id)
                        : 'Peserta #' . $presetSubmission->id;

                    $prizeData['has_preset_winner'] = true;
                    $prizeData['preset_winner'] = [
                        'id' => $presetSubmission->id,
                        'name' => $presetName,
                    ];
                }

                return $prizeData;
            });

        // Total statistik
        $totalCandidates = $form->submissions()->count();
        $totalPrizes = $form->prizes()->count();
        $savedWinners = $form->prizes()->whereHas('winner')->count();
        $availablePrizes = $totalPrizes - $savedWinners;

        return response()->json([
            'candidates' => $allCandidates,
            'prizes' => $allPrizes,
            'stats' => [
                'totalCandidates' => $totalCandidates,
                'totalPrizes' => $totalPrizes,
                'availableCandidates' => $allCandidates->count(),
                'availablePrizes' => $availablePrizes,
                'savedWinners' => $savedWinners,
            ]
        ]);
    }

    public function peserta()
    {
        $submissions = FormSubmission::with(['form', 'winners'])
            ->latest()
            ->get();

        $forms = Form::orderBy('title')->get();

        return view('admin.peserta', compact('submissions', 'forms'));
    }

    public function pemenang(Request $request)
    {
        // Sort option
        $sort = $request->query('sort', 'latest');

        $sortMap = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'az' => ['title', 'asc'],
            'za' => ['title', 'desc'],
        ];

        [$column, $direction] = $sortMap[$sort] ?? $sortMap['latest'];
        // Load forms dengan semua submissions (untuk hitungan) dan prizes
        $forms = Form::with([
            'prizes' => function ($q) {
                $q->with(['winner.submission', 'presetSubmission', 'category']);
            },
            'prizeCategories.prizes', // Load kategori beserta hadiah-hadiahnya
            'submissions.winners' // Load semua submissions dengan winners (plural)
        ])->where('is_active', true)
            ->orderBy($column, $direction)
            ->get();

        // Semua submissions eligible (peserta bisa menang berkali-kali)
        foreach ($forms as $form) {
            $form->eligibleSubmissions = $form->submissions;
        }

        $winners = Winner::with(['submission.form', 'prize', 'selector'])
            ->latest()
            ->get();

        $totalPemenang = Winner::count();
        $pemenangHariIni = Winner::whereDate('created_at', today())->count();
        $totalHadiah = Prize::count();
        $hadiahTersedia = Prize::whereDoesntHave('winner')->count();

        return view('admin.pemenang', compact(
            'forms',
            'winners',
            'totalPemenang',
            'pemenangHariIni',
            'totalHadiah',
            'hadiahTersedia'
        ));
    }

    public function winners(Request $request)
    {
        // Sort option
        $sort = $request->query('sort', 'latest');

        $sortMap = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'az' => ['title', 'asc'],
            'za' => ['title', 'desc'],
        ];

        [$column, $direction] = $sortMap[$sort] ?? $sortMap['latest'];
        // Load forms dengan semua submissions (untuk hitungan) dan prizes
        $forms = Form::with([
            'prizes' => function ($q) {
                $q->with(['winner.submission', 'presetSubmission', 'category']);
            },
            'prizeCategories.prizes', // Load kategori beserta hadiah-hadiahnya
            'submissions.winners' // Load semua submissions dengan winners (plural)
        ])->where('is_active', true)
            ->orderBy($column, $direction)
            ->get();

        // Semua submissions eligible (peserta bisa menang berkali-kali)
        foreach ($forms as $form) {
            $form->eligibleSubmissions = $form->submissions;
        }

        $winners = Winner::with(['submission.form', 'prize', 'selector'])
            ->latest()
            ->get();

        $totalPemenang = Winner::count();
        $pemenangHariIni = Winner::whereDate('created_at', today())->count();
        $totalHadiah = Prize::count();
        $hadiahTersedia = Prize::whereDoesntHave('winner')->count();

        return view('admin.winners', compact(
            'forms',
            'winners',
            'totalPemenang',
            'pemenangHariIni',
            'totalHadiah',
            'hadiahTersedia'
        ));
    }

    /**
     * Halaman Kelola Hadiah - untuk role input_hadiah
     * Menampilkan daftar form dan hadiah tanpa fitur pemenang
     */
    public function hadiah(Request $request)
    {
        // Sorting logic
        $sort = $request->get('sort', 'latest');
        $column = 'created_at';
        $direction = 'desc';

        switch ($sort) {
            case 'oldest':
                $column = 'created_at';
                $direction = 'asc';
                break;
            case 'az':
                $column = 'title';
                $direction = 'asc';
                break;
            case 'za':
                $column = 'title';
                $direction = 'desc';
                break;
            default: // 'latest'
                $column = 'created_at';
                $direction = 'desc';
                break;
        }

        // Load forms dengan prizes
        $forms = Form::with([
            'prizes' => function ($q) {
                $q->with(['winner.submission', 'category']);
            },
            'prizeCategories.prizes', // Load kategori beserta hadiah-hadiahnya
            'submissions'
        ])->where('is_active', true)
            ->orderBy($column, $direction)
            ->get();

        $totalHadiah = Prize::count();
        $hadiahTersedia = Prize::whereDoesntHave('winner')->count();
        $hadiahTerisi = Prize::whereHas('winner')->count();

        return redirect()->route('admin.pemenang');
        
        return view('admin.hadiah', compact(
            'forms',
            'totalHadiah',
            'hadiahTersedia',
            'hadiahTerisi'
        ));
    }

    public function forms()
    {
        $forms = Form::withCount('submissions')
            ->latest()
            ->paginate(20);

        return view('admin.forms', compact('forms'));
    }

    public function laporan(Request $request)
    {
        $query = FormSubmission::with(['form.fields'])->latest();


        if ($request->filled('form_id')) {
            $query->where('form_id', $request->form_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('submission_data', 'LIKE', "%$search%");
        }

        $submissions = $query->paginate(20)->withQueryString();
        $forms = Form::all();

        $totalPeserta = FormSubmission::count();
        $totalPemenang = Winner::count();
        $totalForm = Form::where('is_active', true)->count();

        $winners = Winner::with(['submission.form', 'prize', 'selector'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.laporan', compact('submissions', 'forms', 'totalPeserta', 'totalPemenang', 'totalForm', 'winners'));
    }

    public function exportLaporan()
    {
        $winners = Winner::with(['submission.form', 'prize', 'selector'])
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-pemenang-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($winners) {
            $file = fopen('php://output', 'w');


            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['No', 'Pemenang', 'Hadiah', 'Form', 'Oleh'], ';');

            foreach ($winners as $index => $winner) {
                $wData = $winner->submission->submission_data ?? [];

                $wName = null;
                foreach ($wData as $key => $value) {
                    if (stripos($key, 'nama') !== false) {
                        $wName = $value;
                        break;
                    }
                }
                if (!$wName) {
                    $wName = 'Peserta #' . $winner->submission->id;
                }

                fputcsv($file, [
                    $index + 1,
                    $wName,
                    $winner->prize->name ?? $winner->prize_name ?? '-',
                    $winner->submission->form->title ?? '-',
                    $winner->selector->name ?? '-',

                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function exportLaporanPdf()
    {
        $winners = Winner::with(['submission.form', 'prize', 'selector'])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('admin.laporan_pdf', compact('winners'));

        // return $pdf->download('laporan-pemenang-' . date('Y-m-d') . '.pdf');

        // menampilkan pdf di browser
        return $pdf->stream('laporan-pemenang-' . date('Y-m-d') . '.pdf');
    }
}
