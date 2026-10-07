<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /** @var list<string> */
    private const SUPPORTED = ['id', 'en'];

    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale', 'id');

        if (! in_array($locale, self::SUPPORTED)) {
            $locale = 'id';
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return redirect()->back();
    }
}
