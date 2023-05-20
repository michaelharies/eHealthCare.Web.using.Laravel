@extends('layouts.auth.default')
@section('content')
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{__('auth.reset_title')}}</p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

            @php $flag = 0; @endphp
            @if(!$flag)
                <div class="input-group mb-3">
                    <input value="{{ old('email') }}" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id = "email" name="email" placeholder="{{__('auth.email')}}" aria-label="{{__('auth.email')}}">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    @if ($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>
                <div class="row mb-3 ">
                    <div class="col-9 m-auto">
                        <button id = "link_button" class="btn btn-{{setting("main_color","primary")}} btn-block"><p style="color: #7c287d">Send password reset link</p></button>
                    </div>
                </div>
            @endif
            <div class="modal fade" id="demoModal" aria- labelledby="demoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <input value="{{ old('password') }}" id = "password" type="text" class="form-control  {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{__('auth.password')}}" aria-label="{{__('auth.password')}}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                            </div>
                            <div class="input-group mb-3 _two">
                                <input id = "confirm_password" value="{{ old('password_confirmation') }}" type="text" class="form-control  {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" placeholder="{{__('auth.password_confirmation')}}" aria-label="{{__('auth.password_confirmation')}}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                @if ($errors->has('password_confirmation'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>
                            <div class="row mb-3 ">
                                <div class="col-9 m-auto">
                                    <button id = "reset_button" class="btn btn-{{setting("main_color","primary")}} btn-block"><p style="color: #7c287d">Reset</p></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="asdf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="asdf-content">

        </div>
    </div>
</div>

        <p class="mb-0 text-center">
            <a href="{{ url('/login') }}" class="text-center" style="color: #000000">Click here to Log in</a>
        </p>
    </div>
@endsection


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript">


  $(function () {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    var email = "";
    var password = "";
    var confirm_password = "";
    var randomNum = "";
    $("#reset_button").click(function(){
        password = $("#password").val();
        if(email != "admin@gmail.com"){
            if(password.toString() == randomNum.toString()){
                $.ajax({
                    method: "post",
                    url: "/userpasswordreset",
                    data: ({
                        "_token": "{{ csrf_token() }}",
                        email: email,
                        password: password
                    }),
                    success: function (res) {
                        if(res.status == "success"){
                            toastr.success("Reset successfully", "System info")
                            $("#demoModal").modal("hide");
                        }
                    },
                    error: function (res) {
                        toastr.error("Input email", "Error")
                    }
                });                   
            }else{
                toastr.error("Dismatch password with system info", "Error")
            }
         
        }else{
            toastr.error("You can not access to admin permission", "Error")
        }

    });

    $('#link_button').click(function(){
        email = $("#email").val();
        if(email){
            if(email == "admin@gmail.com" || email == "haider@gmail.com")toastr.error("You can not access to admin permission", "Error")
            else{
                let number_password = Math.random() * 1000000;
                number_password = number_password.toString();
                number_password = number_password.split(".")[0];
                randomNum = number_password;

                $("#demoModal").modal("show");
                toastr.info('Please wait....')
                toastr.options.timeOut = 10000;
                setTimeout(function () {
                    toastr.success('Reset with this number',randomNum);
                }, 5000);
            }

        }
        else{
            toastr.error("Input email", "Error")
        }
    });
  });
</script>

<style>
    .modal {
        border-radius: 20px;
        margin-top: 373px;
        top: 300px;
        right: 100px;
        bottom: 0;
        left: 0;
        z-index: 10040;
        overflow: auto;
        overflow-y: auto;
    }
    ._two{
        display: none!important;
    }
</style>