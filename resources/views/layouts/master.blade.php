<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
    @if(\Auth::check())
      <header>
        <form class="" action="\logout" method="POST">
          @csrf
         <button type="submit" class="bg-black text-white px-3 py-2 font-bold rounded float-right m-5">LogOut</button>
        </form>
      </header>
   @endif
    <div id="app">
        @yield('body')
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>