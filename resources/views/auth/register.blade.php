@extends('layouts.auth.default')
@section('content')
    <div class="card-body login-card-body">

        <form action="{{ url('/register') }}" method="post">
            {!! csrf_field() !!}

            <div class="input-group mb-3">
                <input value="{{ old('name') }}" type="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" placeholder="{{__('auth.name')}}" aria-label="{{__('auth.name')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                @if ($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <input value="{{ old('email') }}" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{__('auth.email')}}" aria-label="{{__('auth.email')}}">
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

            <div class="input-group mb-3">
                <input value="{{ old('password_confirmation') }}" type="password" class="form-control  {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" placeholder="{{__('auth.password_confirmation')}}" aria-label="{{__('auth.password_confirmation')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                @if ($errors->has('password_confirmation'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                @endif
            </div>

            <div class="row mb-2">
                <div class="col-12">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember"> <label for="remember">
                            By signing up, you agree to comiere's <span style="color: #7c287d">Privacy Policy</span> and <span style="color: #7c287d">Terms of Use</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 20px; width:100px">SIGN UP</button>
                    </div>
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

        <p class="mb-1 text-center">
            <a href="{{ url('/login') }}"><span style="color: #a79da7">Already have an account?</span> <span style="color: #7c287d">Sign In</span></a>
        </p>
    </div>
    <!-- /.login-card-body -->
@endsection
