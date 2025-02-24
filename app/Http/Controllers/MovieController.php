<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\OmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    private $omdbService;

    public function __construct(OmdbService $omdbService)
    {
        $this->omdbService = $omdbService;
    }

    private function filterMoviesWithPosters($movies)
    {
        return array_filter($movies, function($movie) {
            return isset($movie['Poster']) && $movie['Poster'] !== 'N/A';
        });
    }

    public function index()
    {
        $scifiMovies = Cache::remember('featured_scifi_movies', 1440, function () {
            $results = $this->omdbService->searchMovies('sci-fi', 1);
            return $this->filterMoviesWithPosters($results['Search'] ?? []);
        });

        $actionMovies = Cache::remember('featured_action_movies', 1440, function () {
            $results = $this->omdbService->searchMovies('action', 1);
            return $this->filterMoviesWithPosters($results['Search'] ?? []);
        });

        $adventureMovies = Cache::remember('featured_adventure_movies', 1440, function () {
            $results = $this->omdbService->searchMovies('adventure', 1);
            return $this->filterMoviesWithPosters($results['Search'] ?? []);
        });

        // Busca os filmes registrados com paginação
        $page = max(1, intval(request()->input('page', 1)));
        $perPage = 4;

        $registeredMovies = Movie::latest()
            ->paginate($perPage)
            ->through(function($movie) {
                return [
                    'Title' => $movie->title,
                    'Year' => $movie->year,
                    'Poster' => asset('storage/' . $movie->poster_path),
                    'imdbID' => 'local_' . $movie->id,
                ];
            });

        return view('movies.index', [
            'movies' => $registeredMovies->items(),
            'totalResults' => $registeredMovies->total(),
            'page' => $registeredMovies->currentPage(),
            'scifiMovies' => array_slice($scifiMovies, 0, 5),
            'actionMovies' => array_slice($actionMovies, 0, 5),
            'adventureMovies' => array_slice($adventureMovies, 0, 5),
            'isRegisteredPage' => true
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $page = max(1, intval($request->input('page', 1)));

        if (empty($search)) {
            return redirect()->route('movies.index');
        }

        // Cache dos resultados por 5 minutos
        $cacheKey = "search_{$search}_page_{$page}";
        $results = Cache::remember($cacheKey, 300, function () use ($search, $page) {
            return $this->omdbService->searchMovies($search, $page);
        });

        $filteredMovies = $this->filterMoviesWithPosters($results['Search'] ?? []);
        
        // Busca nos filmes locais
        $localMovies = Movie::where(function($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('plot', 'like', "%{$search}%");
        })
        ->get()
        ->map(function($movie) {
            return [
                'Title' => $movie->title,
                'Year' => $movie->year,
                'Poster' => asset('storage/' . $movie->poster_path),
                'imdbID' => 'local_' . $movie->id,
            ];
        })
        ->toArray();

        // Combina os resultados
        $allMovies = $page === 1 
            ? array_merge($localMovies, $filteredMovies)  // filmes locais primeiro
            : $filteredMovies;  // resultados da API

        $totalResults = ($results['totalResults'] ?? 0) + ($page === 1 ? count($localMovies) : 0);
        
        return view('movies.index', [
            'movies' => $allMovies,
            'totalResults' => $totalResults,
            'search' => $search,
            'page' => $page,
            'scifiMovies' => [],
            'actionMovies' => [],
            'adventureMovies' => []
        ]);
    }

    public function show($imdbId)
    {
        if (str_starts_with($imdbId, 'local_')) {
            $id = substr($imdbId, 6);
            $movie = Movie::findOrFail($id);
            return view('movies.show', [
                'movie' => [
                    'Title' => $movie->title,
                    'Year' => $movie->year,
                    'Plot' => $movie->plot,
                    'Genre' => $movie->genre,
                    'Poster' => asset('storage/' . $movie->poster_path),
                    'imdbID' => $imdbId,
                    'Runtime' => 'N/A',
                    'Director' => 'N/A',
                    'Writer' => 'N/A',
                    'Actors' => 'N/A',
                    'Language' => 'N/A',
                    'Country' => 'N/A',
                    'Awards' => 'N/A',
                    'Ratings' => [],
                    'Type' => 'movie',
                    'imdbRating' => 'N/A',
                    'imdbVotes' => 'N/A',
                    'Rated' => 'N/A'
                ]
            ]);
        }

        $movie = $this->omdbService->searchByImdbId($imdbId);
        return view('movies.show', [
            'movie' => $movie
        ]);
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Iniciando salvamento de filme', $request->except('poster'));
            \Log::info('Tamanho do arquivo:', [
                'size' => $request->file('poster')->getSize(),
                'max_size' => ini_get('upload_max_filesize')
            ]);
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
                'genre' => 'required|string|max:255',
                'plot' => 'required|string',
                'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            if (!$request->hasFile('poster')) {
                throw new \Exception('O arquivo de poster é obrigatório.');
            }

            $poster = $request->file('poster');
            
            if (!$poster->isValid()) {
                throw new \Exception('O arquivo de poster está corrompido ou é inválido.');
            }

            if ($poster->getSize() > 5120 * 1024) {
                throw new \Exception('O arquivo não pode ser maior que 5MB.');
            }

            try {
                $posterPath = $poster->store('posters', 'public');
                if (!$posterPath) {
                    throw new \Exception('Falha ao salvar o arquivo do poster.');
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao salvar poster: ' . $e->getMessage());
                throw new \Exception('Não foi possível salvar o poster. ' . $e->getMessage());
            }

            try {
                $movie = Movie::create([
                    'title' => $validated['title'],
                    'year' => $validated['year'],
                    'genre' => $validated['genre'],
                    'plot' => $validated['plot'],
                    'poster_path' => $posterPath,
                ]);
            } catch (\Exception $e) {
                // Se falhou ao criar o filme, remove o poster
                Storage::disk('public')->delete($posterPath);
                throw new \Exception('Erro ao salvar o filme no banco de dados: ' . $e->getMessage());
            }

            return redirect()->route('movies.show', 'local_' . $movie->id)
                ->with('success', 'Filme adicionado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar filme: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput($request->except('poster'));
        }
    }

    public function registered()
    {
        $page = max(1, intval(request()->input('page', 1)));
        $perPage = 4;

        $localMovies = Movie::latest()
            ->paginate($perPage)
            ->through(function($movie) {
                return [
                    'Title' => $movie->title,
                    'Year' => $movie->year,
                    'Plot' => $movie->plot,
                    'Genre' => $movie->genre,
                    'Poster' => asset('storage/' . $movie->poster_path),
                    'imdbID' => 'local_' . $movie->id,
                ];
            });

        return view('movies.index', [
            'movies' => $localMovies->items(),
            'totalResults' => $localMovies->total(),
            'page' => $localMovies->currentPage(),
            'search' => null,
            'scifiMovies' => [],
            'actionMovies' => [],
            'adventureMovies' => [],
            'isRegisteredPage' => true
        ]);
    }

    public function destroy($imdbId)
    {
        try {
            if (!str_starts_with($imdbId, 'local_')) {
                throw new \Exception('Apenas filmes locais podem ser removidos.');
            }

            $id = substr($imdbId, 6);
            $movie = Movie::findOrFail($id);
            
            // Remove o poster do storage
            if ($movie->poster_path) {
                Storage::disk('public')->delete($movie->poster_path);
            }
            
            // Remove o registro do banco
            $movie->delete();

            return redirect()->route('movies.registered')
                ->with('success', 'Filme removido com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao remover filme: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao remover o filme: ' . $e->getMessage());
        }
    }
} 