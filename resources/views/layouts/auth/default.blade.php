<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{setting('app_name')}} | {{setting('app_short_description')}}</title>
    <link rel="icon" type="image/png" href="{{$app_logo ?? ''}}"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,600&display=fallback">
    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />

    @stack('js_lib')
</head>
<body class="hold-transition login-page">
<div class="login-box" @if(isset($width)) style="width:{{$width}}" @endif>
    <div class="login-logo" style="height: 60px; padding-top: 10px; background: yellow; border-top-right-radius: 20px; border-top-left-radius: 20px;">
        <a href="{{ url('/') }}"><img src="{{$app_logo}}" alt="{{setting('app_name')}}"></a>
    </div>
    <div class="card shadow-sm">
        @yield('content')
    </div>
</div>
<script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

@stack('scripts')
</body>
</html>
<style>
    body{
        background-image: url("{{asset('images/background.png')}}");
        background-position: center center;
        background-size: cover;
    }
    .card {
    box-shadow: none!important;
    }
    .shadow-sm {
        box-shadow: none!important;
    }
</style>