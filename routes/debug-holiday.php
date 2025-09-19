<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

if (app()->environment(['local', 'development'])) {
    Route::get('/clear-holiday-cache/{year?}/{month?}', function($year = null, $month = null) {
        try {
            if ($year && $month) {
                $cacheKey = "national_holidays_{$year}_{$month}";
                Cache::forget($cacheKey);
                return response()->json([
                    'success' => true,
                    'message' => "Cache cleared for {$month}/{$year}",
                    'cache_key' => $cacheKey
                ]);
            } else {
                // Clear all holiday caches
                $currentYear = now()->year;
                $cleared = [];

                for ($y = $currentYear - 1; $y <= $currentYear + 1; $y++) {
                    for ($m = 1; $m <= 12; $m++) {
                        $cacheKey = "national_holidays_{$y}_{$m}";
                        Cache::forget($cacheKey);
                        $cleared[] = $cacheKey;
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'All holiday caches cleared',
                    'cleared_keys' => $cleared
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('clear.holiday.cache');

    Route::get('/test-holiday-api/{year}/{month}', function($year, $month) {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://api-harilibur.vercel.app/api', [
                'year' => $year,
                'month' => $month
            ]);

            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
                'formatted_holidays' => $response->successful() ? collect($response->json())->map(function($holiday) {
                    return [
                        'date' => $holiday['holiday_date'] ?? 'missing',
                        'name' => $holiday['holiday_name'] ?? 'missing'
                    ];
                }) : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('test.holiday.api');
}
