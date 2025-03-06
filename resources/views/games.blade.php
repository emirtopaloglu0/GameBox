<x-app-layout>
    
    <div class="container mt-4">
        {{-- @dd($ratings) --}}

        {{-- <h1 style="text-align: center">Oyun Listesi</h1>
        <hr> --}}
        <br>
        {{-- Kategorilerine göre sıralama gelecek --}}
        <div class="d-flex  justify-content-center mb-4">
            <select name="years" id="" class="form-select mx-2 w-auto" onchange="">
                <option value="0">YEAR</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>

            <select name="ratings" id="" class="form-select mx-2 w-auto " onchange="">
                <option value="0">RATING</option>
                <option value="1">Highest First</option>
                <option value="2">Lowest First</option>

            </select>

            <select name="genres" id="" class="form-select mx-2 w-auto" onchange="">
                <option value="0">GENRE</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre['id'] }}">{{ $genre['name'] }}</option>
                @endforeach
            </select>

        </div>
        <br>
        <div class="d-lg-flex justify-content-center row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4" style="gap: 15px;">
            @foreach ($games as $game)
                <div class="game-card ">
                    {{-- <h2 style="font-size: larger">{{ $game['name'] }}</h2> --}}
                    <br>
                    @if (isset($game['cover']['url']))
                        @php
                            $highResCover = str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
                        @endphp
                        <img class="rounded-top-3" src="https:{{ $highResCover }}"
                            alt="{{ $game['name'] ?? 'Kapak Yok' }}">
                    @else
                        <p>Kapak resmi bulunamadı</p>
                    @endif

                    <br>
                    {{-- @if (isset($game['total_rating_count']))
                        <p>Oy Sayısı: {{ $game['total_rating_count'] }}</p>
                    @endif --}}

                    @if (isset($game['total_rating']))
                        <p class="small text-muted">Ortalama Puan: {{ round($game['total_rating'], 2) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <br>
        <div class="pagination justify-content-end">
            @if ($page > 1)
                <a href="?page={{ $page - 1 }}" class="page-link">← Önceki Sayfa</a>
            @else
                <a href="" class="page-link disabled">← Önceki Sayfa</a>
            @endif
            @if ($page > 2)
                <a href="?page={{ $page - 2 }}" class="page-link">{{ $page - 2 }}</a>
            @endif
            @if ($page > 1)
                <a href="?page={{ $page - 1 }}" class="page-link">{{ $page - 1 }}</a>
            @endif
            <a href="?page={{ $page }}" class="page-link active">{{ $page }}</a>
            <a href="?page={{ $page + 1 }}" class="page-link">{{ $page + 1 }}</a>
            <a href="?page={{ $page + 2 }}" class="page-link">{{ $page + 2 }}</a>
            <a href="?page={{ $page + 1 }}"class="page-link">Sonraki Sayfa →</a>
        </div>
    </div>
</x-app-layout>


{{-- 
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>Oyun Listesi</title>
    <style>
        body {
            background-color: #14181c;
            color: #fefefe;
        }

        .game-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .game-card {
            width: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
        }

        .game-card img {
            width: 100%;
            height: auto;
        }

        .page-link {
            background-color: #00ac1c;
            color: #f8fdf9;
            border-radius: 5px;
            border-color: #14181c;
        }
    </style>
</head>

<body>
    {{-- @dd($games) --}}
{{-- <div class="container">
    <h1 style="text-align: center">Oyun Listesi</h1>
    <hr>
    <div class="pagination justify-content-end">
        @if ($page > 1)
            <a href="?page={{ $page - 1 }}" class="page-link">← Önceki Sayfa</a>
        @else
            <a href="" class="page-link" style="background-color: #14181c">← Önceki Sayfa</a>
        @endif
        @if ($page > 2)
            <a href="?page={{ $page - 2 }}" class="page-link">{{ $page - 2 }}</a>
        @endif
        @if ($page > 1)
            <a href="?page={{ $page - 1 }}" class="page-link">{{ $page - 1 }}</a>
        @endif
        <a href="?page={{ $page }}" class="page-link active">{{ $page }}</a>
        <a href="?page={{ $page + 1 }}" class="page-link">{{ $page + 1 }}</a>
        <a href="?page={{ $page + 2 }}" class="page-link">{{ $page + 2 }}</a>
        <a href="?page={{ $page + 1 }}"class="page-link">Sonraki Sayfa →</a>
    </div>
    <br>
    <div class="d-lg-flex justify-content-around">
        @foreach ($games as $game)
            <div class="game-card">
                <h3>{{ $game['name'] }}</h3>

                @if (isset($game['cover']['url']))
                    <img src="https:{{ $game['cover']['url'] }}" alt="{{ $game['name'] }}">
                @else
                    <p>Kapak resmi bulunamadı</p>
                @endif
            </div>
        @endforeach
    </div>
    <br>

</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
</body>

</html> --}} --}}
