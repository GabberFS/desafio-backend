<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class OmdbService
{
    private string $apiKey;
    private string $baseUrl;
    private const CACHE_TTL = 1440; // 24 horas em minutos

    public function __construct()
    {
        $this->apiKey = Config::get('services.omdb.key');
        $this->baseUrl = Config::get('services.omdb.url');
    }

    public function searchMovie(string $title)
    {
        $cacheKey = "movie_title_{$title}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($title) {
            $response = Http::get($this->baseUrl, [
                'apikey' => $this->apiKey,
                't' => $title,
            ]);

            return $response->json();
        });
    }

    public function searchByImdbId(string $imdbId)
    {
        $cacheKey = "movie_imdb_{$imdbId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($imdbId) {
            $response = Http::get($this->baseUrl, [
                'apikey' => $this->apiKey,
                'i' => $imdbId,
            ]);

            return $response->json();
        });
    }

    public function searchMovies(string $search, int $page = 1)
    {
        $cacheKey = "movies_search_{$search}_page_{$page}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($search, $page) {
            $response = Http::get($this->baseUrl, [
                'apikey' => $this->apiKey,
                's' => $search,
                'page' => $page,
            ]);

            return $response->json();
        });
    }
} 