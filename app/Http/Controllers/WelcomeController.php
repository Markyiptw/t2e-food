<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;

class WelcomeController extends Controller
{
    private const string IMAGE_DIRECTORY = 'images/hong-kong-view';

    private const array IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function __invoke(): View
    {
        $directory = resource_path(self::IMAGE_DIRECTORY);

        $heroImages = File::isDirectory($directory)
            ? collect(File::files($directory))
                ->filter(fn ($file) => in_array(strtolower($file->getExtension()), self::IMAGE_EXTENSIONS, true))
                ->shuffle()
                ->map(fn ($file) => Vite::asset('resources/'.self::IMAGE_DIRECTORY."/{$file->getFilename()}"))
                ->values()
            : collect();

        return view('welcome', [
            'heroImages' => $heroImages,
        ]);
    }
}
