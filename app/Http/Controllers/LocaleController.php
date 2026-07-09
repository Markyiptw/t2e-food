<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:en,zh-HK'],
        ]);

        return back()->withCookie(
            cookie()->forever('locale', $validated['locale'])
        );
    }
}
