@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 pt-24">
        @if(isset($search))
            <h2 class="text-3xl font-bold mb-8">Resultados para "{{ $search }}"</h2>
            
            @if(isset($movies) && count($movies) > 0)
                <div class="movie-grid">
                    @foreach($movies as $movie)
                        <a href="{{ route('movies.show', $movie['imdbID']) }}" class="card">
                            <div class="movie-card">
                                @if(isset($movie['Poster']) && $movie['Poster'] !== 'N/A')
                                    <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="movie-card-image">
                                @else
                                    <div class="w-full aspect-[2/3] bg-gray-800 flex items-center justify-center">
                                        <span class="text-gray-500">Sem imagem</span>
                                    </div>
                                @endif
                                <div class="movie-card-content">
                                    <h3 class="text-xl font-bold mb-2">{{ $movie['Title'] }}</h3>
                                    <p class="text-gray-400">{{ $movie['Year'] }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if(isset($totalResults) && $totalResults > count($movies))
                    <div class="pagination mt-8 flex items-center justify-center gap-4">
                        @if($page > 1)
                            <a href="{{ route('movies.search', ['search' => $search, 'page' => $page - 1]) }}" class="btn-secondary">Anterior</a>
                        @endif

                        <span class="px-4 py-2 text-gray-400">Página {{ $page }}</span>

                        @if($page * 10 < $totalResults)
                            <a href="{{ route('movies.search', ['search' => $search, 'page' => $page + 1]) }}" class="btn-secondary">Próxima</a>
                        @endif
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-400 text-xl">Nenhum filme encontrado.</p>
                </div>
            @endif
        @else
            <div class="space-y-12">
                @if(isset($movies) && count($movies) > 0)
                    <section>
                        <h2 class="text-2xl font-bold mb-6">Filmes Registrados</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($movies as $movie)
                                <a href="{{ route('movies.show', $movie['imdbID']) }}" class="card">
                                    <div class="movie-card">
                                        @if(isset($movie['Poster']) && $movie['Poster'] !== 'N/A')
                                            <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="movie-card-image">
                                        @else
                                            <div class="w-full aspect-[2/3] bg-gray-800 flex items-center justify-center">
                                                <span class="text-gray-500">Sem imagem</span>
                                            </div>
                                        @endif
                                        <div class="movie-card-content">
                                            <h3 class="text-lg font-bold mb-2">{{ $movie['Title'] }}</h3>
                                            <p class="text-gray-400">{{ $movie['Year'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if(isset($totalResults) && $totalResults > count($movies))
                            <div class="pagination mt-8 flex items-center justify-center gap-4">
                                @if($page > 1)
                                    <a href="{{ route('movies.index', ['page' => $page - 1]) }}" class="btn-secondary flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        Anterior
                                    </a>
                                @endif

                                <span class="px-4 py-2 text-gray-400">Página {{ $page }}</span>

                                @if($page * 4 < $totalResults)
                                    <a href="{{ route('movies.index', ['page' => $page + 1]) }}" class="btn-secondary flex items-center">
                                        Próxima
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </section>
                @endif

                @if(count($scifiMovies) > 0)
                    <section>
                        <h2 class="text-2xl font-bold mb-6">Ficção Científica</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            @foreach($scifiMovies as $movie)
                                <a href="{{ route('movies.show', $movie['imdbID']) }}" class="card">
                                    <div class="movie-card">
                                        @if(isset($movie['Poster']) && $movie['Poster'] !== 'N/A')
                                            <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="movie-card-image">
                                        @else
                                            <div class="w-full aspect-[2/3] bg-gray-800 flex items-center justify-center">
                                                <span class="text-gray-500">Sem imagem</span>
                                            </div>
                                        @endif
                                        <div class="movie-card-content">
                                            <h3 class="text-lg font-bold mb-2">{{ $movie['Title'] }}</h3>
                                            <p class="text-gray-400">{{ $movie['Year'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(count($actionMovies) > 0)
                    <section>
                        <h2 class="text-2xl font-bold mb-6">Ação</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            @foreach($actionMovies as $movie)
                                <a href="{{ route('movies.show', $movie['imdbID']) }}" class="card">
                                    <div class="movie-card">
                                        @if(isset($movie['Poster']) && $movie['Poster'] !== 'N/A')
                                            <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="movie-card-image">
                                        @else
                                            <div class="w-full aspect-[2/3] bg-gray-800 flex items-center justify-center">
                                                <span class="text-gray-500">Sem imagem</span>
                                            </div>
                                        @endif
                                        <div class="movie-card-content">
                                            <h3 class="text-lg font-bold mb-2">{{ $movie['Title'] }}</h3>
                                            <p class="text-gray-400">{{ $movie['Year'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(count($adventureMovies) > 0)
                    <section>
                        <h2 class="text-2xl font-bold mb-6">Aventura</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            @foreach($adventureMovies as $movie)
                                <a href="{{ route('movies.show', $movie['imdbID']) }}" class="card">
                                    <div class="movie-card">
                                        @if(isset($movie['Poster']) && $movie['Poster'] !== 'N/A')
                                            <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="movie-card-image">
                                        @else
                                            <div class="w-full aspect-[2/3] bg-gray-800 flex items-center justify-center">
                                                <span class="text-gray-500">Sem imagem</span>
                                            </div>
                                        @endif
                                        <div class="movie-card-content">
                                            <h3 class="text-lg font-bold mb-2">{{ $movie['Title'] }}</h3>
                                            <p class="text-gray-400">{{ $movie['Year'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const swiperOptions = {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            1280: {
                slidesPerView: 4,
                spaceBetween: 20,
            }
        },
    };

    new Swiper('.featured-scifi', swiperOptions);
    new Swiper('.featured-action', swiperOptions);
    new Swiper('.featured-adventure', swiperOptions);
});
</script>
@endpush 