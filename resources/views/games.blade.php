<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>Oyun Listesi</title>
    <style>
        .game-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .game-card {
            width: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .game-card img {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <h1>Oyun Listesi</h1>
    {{-- @dd($games) --}}
    <div class="game-container">
        @foreach ($games as $game)
            <div class="game-card">
                <h3>{{ $game['name'] }}</h3>
                @if (isset($game['cover']['url']))
                    <img src="https:{{ $game['cover']['url'] }}" alt="{{ $game['name'] }}">
                @else
                    <p>Kapak resmi bulunamadı</p>
                @endif
                @if (isset($game['popularity']))
                    <p>Popülerlik: {{ round($game['popularity'], 2) }}</p>
                @endif
            </div>
        @endforeach
    </div>


    <div class="pagination">
        @if ($page > 1)
            <a href="?page={{ $page - 1 }}" class="page-link">← Önceki Sayfa</a>
        @endif
        <a href="?page={{ $page + 1 }}"class="page-link">Sonraki Sayfa →</a>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
