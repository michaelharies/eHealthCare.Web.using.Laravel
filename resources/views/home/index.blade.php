@extends('layouts.app')

@section('content')
@php
use Carbon\Carbon;
@endphp
<div class="homeBody">

    <div class="searcharea">
        <div class="row justifycontentspacearound">
            <div>
                <h1>Welcome to Comiere</h1>
            </div>
        </div>
        <br />
        <div class="row justifycontentspacearound">
            <h3>Find, compare and book in-network doctors</h3>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8 form-group">
                <form action="{{action('HomeController@searching')}}" method="get">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="text" class="form-control" id="search_text"
                            placeholder="Search doctors with name, speciality, address...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-md"></div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="1" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc">Allergists</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="2" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> Oncologists</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="3" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc">Ophthalmologists</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="4" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> Neurologists</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="5" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> Hematologists</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="6" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> DentalSurgeons</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="9" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> Veterinarian</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="7" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> Cardiovascular</p>
                    </div>
                </button>
            </form>
        </div>
        <div class="col-md">
            <form action="{{action('HomeController@speciality')}}" method="get">
                @csrf
                <input type="text" value="8" name="id" class="form-control" style="display: none;" />
                <button class="card backblue" type="submit">
                    <div class="card-body">
                        <p class="fontc"> Otolaryngologists</p>
                    </div>
                </button>
            </form>
        </div>

        <div class="col-md"></div>
    </div>

    <div class="working_content">

        @if($promise)
        <div class="row justifycontentspacearound margin-bottom-10">
            <div>
                <h3 class="fontbold">You have the following appointments.</h3>
            </div>
        </div>
        @endif
        @if(!$promise)
        <div class="row justifycontentspacearound margin-bottom-10">
            <div>
                <h3 class="fontbold">You don't have any appointments.</h3>
            </div>
        </div>
        @endif

        <div class="row promised_area">
            @foreach($promise as $protp)
            @if(!$protp->cancel)
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if(!$protp->userimagepath)
                        <div class="col-md-2">
                            <img class="card-img-top" src="{{asset('images/default.png')}}" alt="Card image">
                        </div>
                        @endif
                        @if($protp->userimagepath)
                        <div class="col-md-2">
                            <img class="card-img-top" src={{"http://127.0.0.1/".$protp->userimagepath}} alt="Card
                            image">
                        </div>
                        @endif
                        <div class="col-md-5">
                            <div class="row">
                                @php $strtp = Carbon::parse($protp->appointment_at)->dayName.",
                                ".$protp->appointment_at; @endphp
                                <h4 class="">{{$strtp}}</h4>
                            </div>
                            <div class="row displayblock">
                                @php
                                $strtp = json_decode($protp->address)->address;
                                $doctortp = json_decode($protp->doctor)->name;
                                $clinictp = json_decode($protp->clinic)->name;
                                $hint = $protp->hint;
                                @endphp

                                <div>
                                    <p class="textgrey"><i class="fa fa-map-marker" aria-hidden="true"></i> {{$strtp}}
                                    </p>
                                </div>
                                <div>
                                    <p class="textgrey"><i class="fa fa-user-md" aria-hidden="true"></i> {{$doctortp}}
                                    </p>
                                </div>
                                <div>
                                    <p class="textgrey"><i class="fa fa-hospital" aria-hidden="true"></i> {{$clinictp}}
                                    </p>
                                </div>
                                @if($hint)
                                <div>
                                    <p class="textgrey">Reason: {{$hint}}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row margin-bottom-50">
                                <p class="textblue">This appointment is created at {{$protp->created_at}}</p>
                            </div>
                            <div class="row">
                                <button class="btn yellow" data-toggle="modal" style="width:fit-content; border-radius: 0px;"
                                    data-target="#deletemodal{{$protp->id}}" type="button"><i class="fa fa-trash fontsize-40"
                                        aria-hidden="true"></i> Do you cancel a promise with this doctor?</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deletemodal{{$protp->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <form action="{{action('HomeController@promisedelete')}}" method="get">
                                @csrf
                                <input name="id" value={{explode('/', $protp->id)[0]}} style="display:none"/>
                                <div class="row justyright">
                                    <div class="col-md-6 justifycontentspacearound margin-top-10">
                                        Do you really delete?
                                    </div>
                                    <button type="submit" class="btn btn-danger margin5">Delete</button>
                                    <button type="button" class="btn btn-secondary margin5"
                                        data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>


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

        // $('#search_text').change(function(){
        //     $.ajax({
        //         url: '/searching',
        //         method: 'post',
        //         data: {
        //             text: $('#search_text').val(),
        //             _token: "{{ csrf_token() }}"
        //         }
        //     }).done(function (response) {
        //     });
        // });

    });

</script>


<style>
    .homeBody {
        padding-top: 20px;
    }

    .fontsize-40 {
        font-size: 15px;
    }

    .backblue {
        background-color: #e2f0ff !important;
    }

    .searcharea {
        background-color: #e2f0ff;
        margin-left: 200px;
        margin-right: 200px;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    h1 {
        font-weight: 900 !important;
    }

    .justifycontentspacearound {
        justify-content: space-around;
    }

    .working_content {
        padding: 20px;
        padding-left: 200px;
        padding-right: 200px;
    }

    .card {
        width: 100%;
    }

    .card-img-top {
        width: 100px !important;
        height: auto;
    }

    .datearea {
        background-color: yellow;
        width: 100%;
        height: 80px;
    }

    .margin-bottom-10 {
        text-align: center;
        margin-bottom: 12px;
    }

    .margin-bottom-50 {
        text-align: center;
        margin-bottom: 50px;
    }

    .date {
        padding-top: 15px;
        padding-bottom: 0px;
        margin-bottom: 0px;
    }

    .dayofweek {
        padding: 0px;
        margin: 0px;
    }

    p {
        margin: 0px !important;
        padding: 0px;
    }

    .fontbold {
        font-weight: 900;
    }

    .card-body {
        padding: 20px !important;
    }

    .textcenter {
        text-align: center;
    }

    .displayblock {
        display: block !important;
    }

    .yellow {
        width: 60px;
        background-color: rgb(236, 236, 50) !important;
    }

    .textblue {
        color: rgb(78, 78, 235);
    }

    .textgrey {
        color: grey;
    }

    .modal-body {
        margin-top: 20px;
        ;
    }

    .justyright {
        justify-content: right;
    }

    .margin5 {
        margin: 10px;
        ;
    }

    .margin-top-10 {
        margin-top: 15px;
    }

    .modal-dialog {
        top: 300px;
    }

    .fontc {
        font-family: cursive;
    }
</style>