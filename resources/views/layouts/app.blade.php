<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <nav class="fixed top-0 left-0 right-0 bg-gray-900 border-b border-gray-800 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo e Links (Desktop) -->
                <div class="flex items-center">
                    <a href="{{ route('movies.index') }}" class="flex items-center">
                        <img src="{{ asset('images/gd.svg') }}" alt="GD Logo" class="h-8 w-8 text-green-400 hover:text-green-500 transition-colors duration-300" />
                    </a>

                    <div class="hidden md:flex items-center ml-10 space-x-4">
                        <a href="{{ route('movies.index') }}" class="nav-link {{ request()->routeIs('movies.index') && !request()->has('search') && !request()->routeIs('movies.registered') ? 'text-green-400' : '' }}">
                            Início
                        </a>
                        <a href="{{ route('movies.registered') }}" class="nav-link {{ request()->routeIs('movies.registered') ? 'text-green-400' : '' }}">
                            Registrados
                        </a>
                    </div>
                </div>

                <!-- Busca e Botões (Desktop) -->
                <div class="hidden md:flex items-center space-x-4">
                    <form action="{{ route('movies.search') }}" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Buscar filmes..." class="input-primary" value="{{ request('search') }}">
                        <button type="submit" class="search-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    <button type="button" class="btn-primary" onclick="openModal('newMovieModal')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden md:inline ml-2">Novo Filme</span>
                    </button>
                </div>

                <!-- Menu Mobile -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-400 hover:text-white" onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path class="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path class="close-icon hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Menu Mobile Dropdown -->
            <div class="mobile-menu hidden md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('movies.index') }}" class="mobile-nav-link {{ request()->routeIs('movies.index') && !request()->has('search') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                        Início
                    </a>
                    <a href="{{ route('movies.registered') }}" class="mobile-nav-link {{ request()->routeIs('movies.registered') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                        Registrados
                    </a>
                    <form action="{{ route('movies.search') }}" method="GET" class="mt-4">
                        <input type="text" name="search" placeholder="Buscar filmes..." class="w-full input-primary" value="{{ request('search') }}">
                    </form>
                    <button type="button" class="w-full btn-primary mt-4" onclick="openModal('newMovieModal')">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Filme
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-16">
        @if(session('success'))
            <div class="fixed top-20 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="success-message">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @if(session('error'))
            <div class="fixed top-20 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="error-message">
                {{ session('error') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('error-message').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @yield('content')
    </main>

    <!-- Modal de Novo Filme -->
    <div id="newMovieModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Adicionar Novo Filme</h3>
                <button type="button" class="text-gray-400 hover:text-white" onclick="closeModal('newMovieModal')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data" class="modal-body">
                @csrf
                <div class="form-group">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" id="title" name="title" class="input-primary" required value="{{ old('title') }}">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="year" class="form-label">Ano</label>
                    <input type="number" id="year" name="year" class="input-primary" required min="1900" max="{{ date('Y') }}" value="{{ old('year') }}">
                    @error('year')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="genre" class="form-label">Gênero</label>
                    <select id="genre" name="genre" class="input-primary" required>
                        <option value="">Selecione um gênero</option>
                        <option value="Ação" {{ old('genre') == 'Ação' ? 'selected' : '' }}>Ação</option>
                        <option value="Aventura" {{ old('genre') == 'Aventura' ? 'selected' : '' }}>Aventura</option>
                        <option value="Comédia" {{ old('genre') == 'Comédia' ? 'selected' : '' }}>Comédia</option>
                        <option value="Drama" {{ old('genre') == 'Drama' ? 'selected' : '' }}>Drama</option>
                        <option value="Ficção Científica" {{ old('genre') == 'Ficção Científica' ? 'selected' : '' }}>Ficção Científica</option>
                        <option value="Romance" {{ old('genre') == 'Romance' ? 'selected' : '' }}>Romance</option>
                        <option value="Terror" {{ old('genre') == 'Terror' ? 'selected' : '' }}>Terror</option>
                    </select>
                    @error('genre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="plot" class="form-label">Sinopse</label>
                    <textarea id="plot" name="plot" class="input-primary" rows="4" required>{{ old('plot') }}</textarea>
                    @error('plot')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="poster" class="form-label">Poster</label>
                    <input type="file" id="poster" name="poster" class="input-primary" accept="image/*" required>
                    @error('poster')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('newMovieModal')">Cancelar</button>
                    <button type="submit" class="btn-primary" id="submitButton">
                        <span class="loading-spinner hidden"></span>
                        <span class="button-text">Salvar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const menuIcon = document.querySelector('.menu-icon');
            const closeIcon = document.querySelector('.close-icon');
            
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        // Adiciona loading state ao formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = document.getElementById('submitButton');
            const loadingSpinner = submitButton.querySelector('.loading-spinner');
            const buttonText = submitButton.querySelector('.button-text');
            
            submitButton.disabled = true;
            loadingSpinner.classList.remove('hidden');
            buttonText.textContent = 'Salvando...';
        });
    </script>
</body>
</html> 