<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Mail\VolunteerRegistrationMail;
use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\DonationChannel;
use App\Models\Founder;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\OrgCategory;
use App\Models\Partnership;
use App\Models\Program;
use App\Models\RunningText;
use App\Models\SiteSettings;
use App\Models\Volunteer;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use App\Services\WaterQualityAssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WebController extends Controller
{
    private function settings()
    {
        return SiteSettings::first();
    }

    public function home()
    {
        $settings = $this->settings();

        return Inertia::render('Home', [
            'slides' => HeroSlide::where('is_active', true)->orderBy('order')->get(),
            'runningTexts' => RunningText::where('is_active', true)->orderBy('order')->get(),
            'topMembers' => Member::orderByDesc('points')->limit(3)->get(),
            'programs' => Program::orderBy('order')->limit(4)->get(),
            'settings' => $settings,
            'fixedText' => $settings?->getFixedText(),
            'heroFixedText' => $settings?->getFixedText(),
            'homeStats' => [
                'relawan_aktif' => Member::count() + Volunteer::where('status', 'APPROVED')->count(),
                'program_terlaksana' => Program::count(),
                'sampah_terangkut' => $settings->stat_sampah_terangkut ?? '2,4T',
                'rata_rata_ph' => WaterQualityRecord::avg('ph') ? number_format(WaterQualityRecord::avg('ph'), 2) : '—',
                'rata_rata_kejernihan' => WaterQualityRecord::avg('clarity_cm') ? number_format(WaterQualityRecord::avg('clarity_cm') / 100, 2) : '—',
            ],
        ]);
    }

    public function about()
    {
        return Inertia::render('About', [
            'founders' => Founder::orderBy('order')->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function organization()
    {
        return Inertia::render('Organization', [
            'categories' => OrgCategory::with(['people' => fn ($q) => $q->orderBy('order')])->orderBy('order')->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function partnerships()
    {
        return Inertia::render('Partnerships', [
            'partnerships' => Partnership::where('is_active', true)->orderBy('order')->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function members()
    {
        return Inertia::render('Members', [
            'members' => Member::orderByDesc('points')->get(),
            'volunteers' => Volunteer::where('status', 'APPROVED')
                ->select(['id', 'full_name', 'photo_url', 'submitted_at'])
                ->orderByDesc('submitted_at')
                ->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function programs()
    {
        return Inertia::render('Programs', [
            'programs' => Program::with(['activities' => fn ($q) => $q->where('is_published', true)->orderByDesc('published_at')->limit(3)])
                ->orderBy('order')
                ->limit(4)
                ->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function programCategory($slug)
    {
        $program = Program::where('slug', $slug)->firstOrFail();

        return Inertia::render('ProgramCategory', [
            'program' => $program,
            'activities' => Activity::where('program_id', $program->id)
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->paginate(12),
            'settings' => $this->settings(),
        ]);
    }

    public function programDetail($program_slug, $activity_slug)
    {
        $program = Program::where('slug', $program_slug)->firstOrFail();

        return Inertia::render('ProgramDetail', [
            'program' => $program,
            'activity' => Activity::where('program_id', $program->id)
                ->where('slug', $activity_slug)
                ->where('is_published', true)
                ->firstOrFail(),
            'settings' => $this->settings(),
        ]);
    }

    public function blog(Request $request)
    {
        $query = BlogPost::where('is_published', true)
            ->where('category', '!=', 'BASELINE_STUDY')
            ->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return Inertia::render('Blog', [
            'posts' => $query->paginate(9)->withQueryString(),
            'filters' => $request->only('category'),
            'baselineStudyPosts' => BlogPost::where('is_published', true)
                ->where('category', 'BASELINE_STUDY')
                ->orderByDesc('published_at')
                ->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function blogDetail($slug)
    {
        return Inertia::render('BlogDetail', [
            'post' => BlogPost::where('slug', $slug)->where('is_published', true)->firstOrFail(),
            'settings' => $this->settings(),
        ]);
    }

    public function gallery()
    {
        return Inertia::render('Gallery', [
            'items' => GalleryItem::latest()->paginate(12),
            'settings' => $this->settings(),
        ]);
    }

    public function waterQuality(Request $request)
    {
        $perPageOptions = [10, 15, 25, 50];
        $perPageInput = $request->input('per_page', 10);
        $perPage = is_numeric($perPageInput) ? (int) $perPageInput : 10;

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $rangeInput = $request->input('range', 'all');
        $range = is_string($rangeInput) && in_array($rangeInput, ['all', 'week', 'month', 'year'], true)
            ? $rangeInput
            : 'all';

        $samplingPointInput = $request->input('sampling_point');
        $samplingPointId = is_numeric($samplingPointInput) ? (int) $samplingPointInput : null;

        $applyFilters = function ($query) use ($samplingPointId, $range) {
            return $query
                ->when($samplingPointId, fn ($q) => $q->where('water_sampling_point_id', $samplingPointId))
                ->when($range !== 'all', function ($q) use ($range) {
                    $from = match ($range) {
                        'week' => now()->subWeek(),
                        'month' => now()->subMonthNoOverflow(),
                        'year' => now()->subYear(),
                        default => null,
                    };

                    if ($from) {
                        $q->where('recorded_at', '>=', $from);
                    }
                });
        };

        // Dataset grafik: seluruh record pada filter aktif, tidak dibatasi paginasi tabel.
        $records = $applyFilters(WaterQualityRecord::query())
            ->with('samplingPoint:id,name')
            ->orderBy('recorded_at')
            ->orderBy('id')
            ->get([
                'id',
                'recorded_at',
                'tss',
                'ph',
                'tds',
                'dissolved_oxygen',
                'temperature',
                'clarity_cm',
                'water_sampling_point_id',
            ]);

        $paginatedRecords = $applyFilters(WaterQualityRecord::query())
            ->with('samplingPoint:id,name')
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $parameterKeys = array_keys(config('water_quality.parameters'));

        $aggregates = $applyFilters(WaterQualityRecord::query())
            ->selectRaw(collect($parameterKeys)
                ->map(fn ($key) => "AVG({$key}) as avg_{$key}, COUNT({$key}) as count_{$key}")
                ->implode(', '))
            ->first();

        /** @var WaterQualityAssessmentService $assessmentService */
        $assessmentService = app(WaterQualityAssessmentService::class);
        $allResearchReferences = $assessmentService->getActiveReferences();

        $summary = [];

        foreach (config('water_quality.parameters') as $key => $parameter) {
            $average = $aggregates?->{"avg_{$key}"};
            $average = $average !== null ? round((float) $average, $parameter['decimals']) : null;

            $assessment = $assessmentService->assess($key, $average, $parameter);

            // Fetch all study-specific research references for this parameter (both provisional and verified)
            $paramReferences = $allResearchReferences
                ->where('parameter', $key)
                ->values()
                ->toArray();

            $summary[$key] = [
                'key' => $key,
                ...$parameter,
                'average' => $average,
                'count' => (int) ($aggregates?->{"count_{$key}"} ?? 0),
                'status' => $assessment['status'],
                'status_key' => $assessment['status_key'],
                'status_label' => $assessment['label'],
                'status_label_id' => $assessment['label_id'],
                'status_badge' => $assessment['badge'],
                'status_dot' => $assessment['dot'],
                'status_message' => $assessment['message'],
                'comparison_reference' => $assessment['reference'],
                'research_references' => $paramReferences,
            ];
        }

        // Global KPI counts based on transparent research-based metrics
        $kpis = [
            'total_measurements' => $records->count(),
            'within_reference' => 0,
            'outside_reference' => 0,
            'operational_alerts' => 0,
            'insufficient_reference' => 0,
            'not_assessed' => 0,
        ];

        foreach ($summary as $item) {
            match ($item['status']) {
                'within_reference' => $kpis['within_reference']++,
                'outside_reference' => $kpis['outside_reference']++,
                'operational_alert' => $kpis['operational_alerts']++,
                'insufficient_reference' => $kpis['insufficient_reference']++,
                'not_assessed' => $kpis['not_assessed']++,
                default => null,
            };
        }

        return Inertia::render('WaterQuality', [
            'records' => $records,
            'paginatedRecords' => $paginatedRecords,
            'samplingPoints' => WaterSamplingPoint::where('is_active', true)->orderBy('name')->get(),
            'summary' => $summary,
            'kpis' => $kpis,
            'allResearchReferences' => $allResearchReferences,
            'waterQualityConfig' => config('water_quality'),
            'filters' => [
                'sampling_point' => $samplingPointId ?? 'all',
                'range' => $range,
                'per_page' => $perPage,
            ],
            'perPageOptions' => $perPageOptions,
            'settings' => $this->settings(),
        ]);
    }

    /**
     * Tentukan status rata-rata/parameter terhadap ambang riset Danau Matano.
     *
     * @param  array<string, mixed>  $parameter
     */
    private function waterQualityStatus(array $parameter, ?float $value): string
    {
        /** @var WaterQualityAssessmentService $assessmentService */
        $assessmentService = app(WaterQualityAssessmentService::class);
        $res = $assessmentService->assess($parameter['key'] ?? '', $value, $parameter);

        return $res['status'];
    }

    public function joinAndDonate()
    {
        return Inertia::render('JoinDonate', [
            'donationChannels' => DonationChannel::where('is_active', true)->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function submitVolunteer(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'motivation' => 'nullable|string|max:2000',
            'quote' => 'nullable|string|max:300',
            'quote_consent' => [
                'nullable',
                'boolean',
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    if (filled($request->input('quote')) && ! filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                        $fail('Persetujuan publikasi kutipan wajib dicentang jika Anda mengisi kutipan.');
                    }
                },
            ],
        ], [
            'quote.max' => 'Kutipan maksimal 300 karakter.',
        ]);

        $hasQuote = filled($request->input('quote'));
        $quote = $hasQuote ? trim((string) $request->input('quote')) : null;
        $consent = $hasQuote && filter_var($request->input('quote_consent'), FILTER_VALIDATE_BOOLEAN);

        $code = strtoupper(Str::random(3).'-'.Str::random(4).'-'.Str::random(3));

        Volunteer::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'motivation' => $validated['motivation'] ?? null,
            'quote' => $quote,
            'quote_consent' => $consent,
            'registration_code' => $code,
            'status' => 'PENDING',
            'submitted_at' => now(),
        ]);

        Mail::to($validated['email'])->send(new VolunteerRegistrationMail($validated['full_name'], $code));

        return redirect()->back()->with('registration_code', $code);
    }

    public function contact()
    {
        return Inertia::render('Contact', [
            'settings' => $this->settings(),
        ]);
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:3000',
        ]);

        $adminEmail = config('mail.from.address', 'admin@tovamatano.org');
        Mail::to($adminEmail)->send(new ContactFormMail($request->name, $request->email, $request->message));

        return redirect()->back()->with('success', true);
    }
}
