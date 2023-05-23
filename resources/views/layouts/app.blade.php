<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    <title>{{setting('app_name')}} | {{setting('app_short_description')}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/png" href="{{$app_logo ?? ''}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">

    @stack('css_lib')
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/'.setting("theme_color","primary").'.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />

    @yield('css_custom')
</head>

<body class="@if(in_array(app()->getLocale(), ['ar','ku','fa','ur','he','ha','ks'])) rtl @else ltr @endif layout-fixed {{setting('fixed_header',false) ? "layout-navbar-fixed" : ""}} {{setting('fixed_footer',false) ? "layout-footer-fixed" : ""}} sidebar-mini {{setting('theme_color')}} {{setting('theme_contrast','')}}-mode" data-scrollbar-auto-hide="l" data-scrollbar-theme="os-theme-dark">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand {{setting('nav_color','navbar-light navbar-white')}} border-bottom-0" style = "background-color: yellow">
        @if(auth()->user()->memberid !== 'patientid')
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{url('dashboard')}}" class="nav-link" style="color: #837b82; font-weight: bold">{{trans('lang.dashboard')}}</a>
            </li>
        </ul>
        @endif
        @if(auth()->user()->memberid === 'patientid')
        <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('dashboard') !!}" style="color: #837b82; font-weight: bold"><p>Home</p></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{action('PatientController@index')}}" style="color: #837b82; font-weight: bold"><p>Patient</p></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('appointments.index') !!}" style="color: #837b82; font-weight: bold"><p>{{trans('lang.appointment_plural')}}</p></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('addresses.index') !!}" style="color: #837b82; font-weight: bold"><p>{{trans('lang.address_plural')}}</p></a>
                </li>
        </ul>
        @endif
        <ul class="navbar-nav ml-auto">

            @can('notifications.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('notifications*') ? 'active' : '' }}" href="{!! route('notifications.index') !!}"><i class="fas fa-bell" style="color: #ada1ac"></i></a>
                </li>
            @endcan
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" style="color: #ada1ac">
                    <img src="{{auth()->user()->getFirstMediaUrl('avatar','icon')}}" class="brand-image mx-2 img-circle elevation-2" alt="User Image">
                    <i class="fa fas fa-angle-down" style="color: #ada1ac"></i> {!! auth()->user()->name !!}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{route('users.profile')}}" class="dropdown-item"> <i class="fas fa-user mr-2"></i> {{trans('lang.user_profile')}} </a>
                    <div class="dropdown-divider"></div>
                    <a href="{!! url('/logout') !!}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-envelope mr-2"></i> {{__('auth.logout')}}
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    @if(auth()->user()->memberid !== 'patientid')
        @include('layouts.sidebar')
    @endif

    <div class="content-wrapper" style = "height: auto!important;">
        @yield('content')
    </div>

    <footer class="main-footer border-0 shadow-sm">
        <div class="float-sm-right d-none d-sm-block">
            <b>Version</b> {{implode('.',str_split(substr(config('installer.currentVersion','v100'),1,3)))}}
        </div>
        <strong>Copyright © {{date('Y')}} <a href="{{url('/')}}">{{setting('app_name')}}</a>.</strong> All rights reserved.
    </footer>

</div>

<script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>

<script src="{{asset('vendor/bootstrap-v4-rtl/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="{{asset('https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js')}}"></script>

<script src="{{asset('https://www.gstatic.com/firebasejs/7.2.0/firebase-messaging.js')}}"></script>

<script type="text/javascript">@include('vendor.notifications.init_firebase')</script>

<script type="text/javascript">
    const messaging = firebase.messaging();
    navigator.serviceWorker.register("{{url('firebase/sw-js')}}")
        .then((registration) => {
            messaging.useServiceWorker(registration);
            messaging.requestPermission()
                .then(function () {
                    console.log('Notification permission granted.');
                    getRegToken();

                })
                .catch(function (err) {
                    console.log('Unable to get permission to notify.', err);
                });
            messaging.onMessage(function (payload) {
                console.log("Message received. ", payload);
                notificationTitle = payload.data.title;
                notificationOptions = {
                    body: payload.data.body,
                    icon: payload.data.icon,
                    image: payload.data.image
                };
                var notification = new Notification(notificationTitle, notificationOptions);
            });
        });

    function getRegToken(argument) {
        messaging.getToken().then(function (currentToken) {
            if (currentToken) {
                saveToken(currentToken);
                console.log(currentToken);
            } else {
                console.log('No Instance ID token available. Request permission to generate one.');
            }
        })
            .catch(function (err) {
                console.log('An error occurred while retrieving token. ', err);
            });
    }


    function saveToken(currentToken) {
        $.ajax({
            type: "POST",
            data: {'device_token': currentToken, 'api_token': '{!! auth()->user()->api_token !!}'},
            url: '{!! url('api/users',['id'=>auth()->id()]) !!}',
            success: function (data) {

            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function changeLanguage(locale) {
        event.preventDefault();
        document.getElementById('current-language').value = locale;
        document.getElementById('languages-form').submit();
    }
</script>

@stack('scripts_lib')
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script src="{{asset('js/scripts.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

@stack('scripts')
</body>
</html>


<style>
    .navbar-dark .navbar-nav .nav-link {
        color: rgb(129 122 122 / 75%);
    }
    .sidebar{
        background: yellow;
    }

    .content-wrapper {
        transition: margin-left 0s ease-in-out!important;
        margin-left: <?php echo (auth()->user()->memberid === 'patientid') ? '0px' : '250px'; ?> !important;
    }
    .main-footer, .main-header {
        transition: margin-left 0s ease-in-out!important;
        margin-left: <?php echo (auth()->user()->memberid === 'patientid') ? '0px' : '250px'; ?> !important;
    }
</style>