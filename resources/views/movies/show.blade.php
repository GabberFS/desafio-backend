@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 pt-16">
        <div class="bg-gray-900 rounded-lg shadow-xl overflow-hidden border border-gray-800 max-w-5xl mx-auto">
            <div class="md:flex items-start">
                <!-- Coluna da Imagem -->
                <div class="md:w-2/5 flex-shrink-0">
                    @if(isset($movie['Poster']) && $movie['Poster'] !== 'N/A')
                        <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="w-full h-auto object-cover">
                    @else
                        <div class="w-full aspect-[2/3] bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500">Sem imagem</span>
                        </div>
                    @endif
                </div>

                <!-- Coluna das Informações -->
                <div class="md:w-3/5 p-8">
                    <h1 class="text-4xl font-bold mb-4 text-white">{{ $movie['Title'] ?? 'Sem título' }}</h1>
                    
                    <!-- Tags de Informação -->
                    <div class="flex flex-wrap gap-3 mb-8">
                        @if(isset($movie['Year']))
                            <span class="px-3 py-1 bg-gray-800 rounded-full text-green-400 text-sm">
                                {{ $movie['Year'] }}
                            </span>
                        @endif
                        
                        @if(isset($movie['Runtime']) && $movie['Runtime'] !== 'N/A')
                            <span class="px-3 py-1 bg-gray-800 rounded-full text-green-400 text-sm">
                                {{ $movie['Runtime'] }}
                            </span>
                        @endif
                        
                        @if(isset($movie['Rated']) && $movie['Rated'] !== 'N/A')
                            <span class="px-3 py-1 bg-gray-800 rounded-full text-green-400 text-sm">
                                {{ $movie['Rated'] }}
                            </span>
                        @endif

                        @if(isset($movie['Genre']))
                            @foreach(explode(', ', $movie['Genre']) as $genre)
                                <span class="px-3 py-1 bg-gray-800 rounded-full text-green-400 text-sm">
                                    {{ $genre }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <!-- Sinopse -->
                    @if(isset($movie['Plot']))
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold mb-3 text-green-400">Sinopse</h2>
                            <p class="text-gray-300 leading-relaxed">{{ $movie['Plot'] }}</p>
                        </div>
                    @endif

                    <!-- Informações do Filme -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @if(isset($movie['Director']) && $movie['Director'] !== 'N/A')
                            <div>
                                <h2 class="text-lg font-semibold mb-2 text-green-400">Direção</h2>
                                <p class="text-gray-300">{{ $movie['Director'] }}</p>
                            </div>
                        @endif

                        @if(isset($movie['Actors']) && $movie['Actors'] !== 'N/A')
                            <div>
                                <h2 class="text-lg font-semibold mb-2 text-green-400">Elenco Principal</h2>
                                <p class="text-gray-300">{{ $movie['Actors'] }}</p>
                            </div>
                        @endif

                        @if(isset($movie['Awards']) && $movie['Awards'] !== 'N/A')
                            <div>
                                <h2 class="text-lg font-semibold mb-2 text-green-400">Prêmios</h2>
                                <p class="text-gray-300">{{ $movie['Awards'] }}</p>
                            </div>
                        @endif

                        @if(isset($movie['imdbRating']) && $movie['imdbRating'] !== 'N/A')
                            <div>
                                <h2 class="text-lg font-semibold mb-2 text-green-400">Avaliação IMDB</h2>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-2xl font-bold text-white">{{ $movie['imdbRating'] }}/10</span>
                                    @if(isset($movie['imdbVotes']) && $movie['imdbVotes'] !== 'N/A')
                                        <span class="text-gray-400 ml-2">({{ $movie['imdbVotes'] }} votos)</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="max-w-5xl mx-auto mt-8 flex items-center justify-between">
            <a href="{{ url()->previous() }}" class="btn-secondary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>

            @if(str_starts_with($movie['imdbID'], 'local_'))
                <form action="{{ route('movies.destroy', $movie['imdbID']) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('Tem certeza que deseja remover este filme?')">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remover Filme
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection 