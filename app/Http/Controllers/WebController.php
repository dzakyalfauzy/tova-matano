<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\DonationChannel;
use App\Models\Founder;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\OrgCategory;
use App\Models\Program;
use App\Models\RunningText;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebController extends Controller
{
    public function home()
    {
        $slides = HeroSlide::where('is_active', true)->orderBy('order')->get();
        $runningTexts = RunningText::where('is_active', true)->orderBy('order')->get();
        $topMembers = Member::orderByDesc('points')->limit(5)->get();
        $programs = Program::orderBy('order')->limit(4)->get();
        $settings = SiteSettings::first();
        
        return Inertia::render('Home', [
            'slides' => $slides,
            'runningTexts' => $runningTexts,
            'topMembers' => $topMembers,
            'programs' => $programs,
            'settings' => $settings,
        ]);
    }

    public function about()
    {
        $founders = Founder::orderBy('order')->get();
        return Inertia::render('About', [
            'founders' => $founders,
        ]);
    }

    public function organization()
    {
        $categories = OrgCategory::with(['orgPeople' => function ($q) {
            $q->orderBy('order');
        }])->orderBy('order')->get();

        return Inertia::render('Organization', [
            'categories' => $categories,
        ]);
    }

    public function members()
    {
        $members = Member::orderByDesc('points')->get();
        return Inertia::render('Members', [
            'members' => $members,
        ]);
    }

    public function programs()
    {
        $programs = Program::with(['activities' => function ($q) {
            $q->where('is_published', true)->orderByDesc('published_at');
        }])->orderBy('order')->get();

        return Inertia::render('Programs', [
            'programs' => $programs,
        ]);
    }
    
    public function programDetail($slug)
    {
        $activity = Activity::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        return Inertia::render('ProgramDetail', [
            'activity' => $activity,
        ]);
    }

    public function blog(Request $request)
    {
        $query = BlogPost::where('is_published', true)->orderByDesc('published_at');
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $posts = $query->paginate(9);
        
        return Inertia::render('Blog', [
            'posts' => $posts,
            'filters' => $request->only('category'),
        ]);
    }
    
    public function blogDetail($slug)
    {
        $post = BlogPost::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        return Inertia::render('BlogDetail', [
            'post' => $post,
        ]);
    }

    public function gallery()
    {
        $items = GalleryItem::latest()->paginate(12);
        
        return Inertia::render('Gallery', [
            'items' => $items,
        ]);
    }

    public function joinAndDonate()
    {
        $channels = DonationChannel::where('is_active', true)->get();
        
        return Inertia::render('JoinDonate', [
            'donationChannels' => $channels,
        ]);
    }

    public function contact()
    {
        return Inertia::render('Contact');
    }
}
