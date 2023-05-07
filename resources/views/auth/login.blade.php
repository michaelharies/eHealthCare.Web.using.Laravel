@extends('layouts.auth.default')
@section('content')

    <div class="card-body login-card-body">

        <form action="{{ url('/login') }}" method="post">
            {!! csrf_field() !!}

            <div class="input-group mb-3">
                <input value="{{ old('email') }}" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{ __('auth.email') }}" aria-label="{{ __('auth.email') }}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                @if ($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <input value="{{ old('password') }}" type="password" class="form-control  {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{__('auth.password')}}" aria-label="{{__('auth.password')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                @if ($errors->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                @endif
            </div>

            <div class="row mb-2">
                <div class="col-6">
                    <div class="icheck-{{setting("theme_color","primary")}}">
                        <input type="checkbox" id="remember" name="remember"> <label for="remember" style=" color: #7c287d">
                            Remember me
                        </label>
                    </div>
                </div>
                <div class="col-6">
                <p class="mb-0 text-center">
                    <a href="{{ url('/password/reset') }}" class="text-center" style=" color: #7c287d">Fotgot Password</a>
                </p>
                </div>
            </div>
            <div class="row" style="justify-content: space-around">
                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 20px; width: 110px">  SIGN IN  </button>
            </div>
        </form>

        <!-- @if(setting('enable_facebook',false) || setting('enable_google',false) || setting('enable_twitter',false))
            <div class="social-auth-links text-center mb-3">
                <p style="text-transform: uppercase; color: #7c287d">- {{__('lang.or')}} -</p>
                @if(setting('enable_facebook',false))
                    <a href="{{url('login/facebook')}}" class="btn btn-outline-secondary"> <i class="fab fa-facebook mr-2"></i> FACEBOOK
                    </a>
                @endif
                @if(setting('enable_google',false))
                    <a href="{{url('login/google')}}" class="btn btn-outline-secondary"> <i class="fab fa-google mr-2"></i> GOOGLE
                    </a>
                @endif

            </div>
        @endif -->

        <p class="mb-0 text-center">
            <a href="{{ url('/register') }}" class="text-center" style="display:flex padding-left:20px"><span style="color: #a79da7">Don't have an account?</span> <span style="color: #7c287d">Sign Up here!</span></a>
        </p>
    </div>

@endsection


<style>
    .card {
    box-shadow: none!important;
    }
    .shadow-sm {
        box-shadow: none!important;
    }
</style>