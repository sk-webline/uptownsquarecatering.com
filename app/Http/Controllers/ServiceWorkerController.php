<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\Language;
use App\ServiceTranslation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;


class ServiceWorkerController extends Controller
{

    public function getCanteenRoutes()
    {
        return response()->json(config('pwa.routes'));
    }

    private function parseRoutes($content)
    {
        // Implement a logic to parse the $content and extract route information
        // This might involve regular expressions, string manipulation, or other methods

        // Example: Dummy implementation, you need to adjust this based on your actual route file structure
        $matches = [];
        preg_match_all('/Route::([a-zA-Z]+)\(\'([^\']+)\'/i', $content, $matches, PREG_SET_ORDER);

        $routes = [];
        foreach ($matches as $match) {
            $uri = $match[2];
            $lastChar = $uri[strlen($uri) - 1];
            if(strtolower($match[1]) == 'get' && $lastChar != '}'){
                $fullUrl =  url('/application' . $uri); // Change this based on your actual URL structure
                $routes[] = $fullUrl;
            }

        }

        return $routes;
    }
}
