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
                'relawan_aktif' => Member::count() + Volunteer::where('status', 'ACCEPTED')->count(),
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

    public function waterQuality()
    {
        return Inertia::render('WaterQuality', [
            'records' => WaterQualityRecord::with('samplingPoint')->orderBy('recorded_at')->get(),
            'samplingPoints' => WaterSamplingPoint::where('is_active', true)->get(),
            'settings' => $this->settings(),
        ]);
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
        ]);

        $code = strtoupper(Str::random(3).'-'.Str::random(4).'-'.Str::random(3));

        Volunteer::create(array_merge($validated, [
            'registration_code' => $code,
            'status' => 'PENDING',
            'submitted_at' => now(),
        ]));

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
