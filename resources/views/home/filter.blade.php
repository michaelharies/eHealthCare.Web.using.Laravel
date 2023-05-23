@extends('layouts.app')

@section('content')
@php
use Carbon\Carbon;
@endphp
<div class="homeBody">
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
    <div class="working_content">
        <div class="row search_filter_result">
            @if($speciality === "disable")
            <h3>Search Result</h3>
            @endif
            @if($speciality !== "disable")
            <h3>{{$speciality}}</h3>
            @endif
            @php $cn = 0; @endphp
            @foreach($doctors as $tp)
            @php $cn ++; @endphp
            @endforeach
            @if(!$cn)
            <div class="row justifycontentspacearound width100">
                <div>
                    <h3>Nothing to match.</h3>
                </div>
            </div>
            @endif
            @foreach($doctors as $doctor)
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                @if(!$doctor->userimagepath)
                                <div class="col-md-2">
                                    <img class="card-img-top" src="{{asset('images/default.png')}}" alt="Card image">
                                </div>
                                @endif
                                @if($doctor->userimagepath)
                                <div class="col-md-2">
                                    <img class="card-img-top" src={{"http://127.0.0.1/".$doctor->userimagepath}}
                                    alt="Card image">
                                </div>
                                @endif
                                <div class="col-md-9">
                                    <div>
                                        <h3>
                                            {{str_replace('"}', '', str_replace('{"en":"', '', $doctor->name))}}
                                        </h3>
                                    </div>
                                    <div>
                                        <p>{{$doctor->specialist}}</p>
                                    </div>
                                    @if($doctor->address)
                                    <div>
                                        <p class="grey"><i class="fa fa-map-marker blue" aria-hidden="true"></i>
                                            {{$doctor->address[0]}}
                                        </p>
                                    </div>
                                    @endif
                                    @if($doctor->review)
                                    <div>
                                        <p class="grey">Reviews({{count($doctor->review)}})</p>
                                    </div>
                                    @endif
                                    @if(!$doctor->review)
                                    <div>
                                        <p class="grey"><i class="fa fa-star blue" aria-hidden="true"></i> Reviews(0)
                                        </p>
                                    </div>
                                    @endif
                                    <div>
                                        <u class="grey" data-toggle="modal"
                                            data-target="#showdetail{{$doctor->id}}">view detail</u>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                @for($par = 0 ; $par < 12 ; $par ++) @php $add='+' .$par.' days';
                                    $date=date('Y-m-d',strtotime($add)) @endphp <div class="col-md-2 margin-bottom-10">
                                    @php $id = $doctor->id.":".Carbon::parse($date)->dayName.':'.$date; @endphp
                                    <div class="datearea" onclick="opendoctordetail('{{ $id }}')" id={{$id}}>
                                        <p class="date">{{$date}}</p>
                                        <p class="dayofweek">{{Carbon::parse($date)->dayName}}</p>
                                    </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="showdetail{{$doctor->id}}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row justifycontentspacearound">
                            <div class="col-md-5"></div>
                            @if(!$doctor->userimagepath)
                            <div class="col-md-2">
                                <img class="card-img-top" src="{{asset('images/default.png')}}" alt="Card image">
                            </div>
                            @endif
                            @if($doctor->userimagepath)
                            <div class="col-md-2">
                                <img class="card-img-top" src={{"http://127.0.0.1/".$doctor->userimagepath}}
                                alt="Card image">
                            </div>
                            @endif
                            <div class="col-md-5"></div>
                            <h3 class="peachpuff">{{explode('"', $doctor->name)[3]}}</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="bold">Specialisties</p>
                                <p class="grey">{{$doctor->specialist}}</p>
                                <br />
                                <p class="bold">My addresses</p>
                                @if($doctor->address)
                                @foreach($doctor->address as $tp)
                                <p class="grey">{{$tp}}</p>
                                @endforeach
                                @endif
                                <br />
                                <p class="bold">My reviews({{count($doctor->review)}})</p>
                                @foreach($doctor->review as $tp)
                                <p class="gery">{{$tp}}</p>
                                @endforeach
                                <br />
                            </div>
                            <div class="col-md-6">
                                <p class="bold">Experiences</p>
                                @foreach($doctor->experience as $tp)
                                <p class="black">{{explode('"', $tp->title)[3]}}</p>
                                <p class="grey">{{explode('<', explode('>', $tp->description)[1])[0]}}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <!-- Modal -->
    <div class="modal fade" id="bookingmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="color:rgb(139, 139, 230)" class="modal-title" id="exampleModalLabel">
                        Please book on your available time.
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row justifycontentspacearound">
                        <div class="col-md-5"></div>
                        <div class="col-md-2">
                            <img class="card-img-top" id="doctorImage" src="{{asset('images/default.png')}}"
                                alt="Card image">
                        </div>
                        <div class="col-md-5"></div>
                        <h3 id="doctorname" class="grey"></h3>
                    </div>
                    <div class="row ">
                        <h5 class="grey" style="margin-left: 10px;">Select patient</h5>
                    </div>
                    <div class="row margin-bottom-10" id="patients">
                    </div>
                    <div class="row ">
                        <h5 class="grey" style="margin-left: 10px;">Select time</h5>
                    </div>
                    <div class="row margin-bottom-10" id="timesarea">
                    </div>
                    <div class="row ">
                        <h5 class="grey" style="margin-left: 10px;">Select doctor address or patient address</h5>
                    </div>
                    <div class="row" id="doctoraddress">
                    </div>
                    <div class="row margin-bottom-10" id="patientaddress">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="text" class="form-control" id="hint"
                            placeholder="What's the reason for your visit?">
                    </div>
                    <div class="row" style="justify-content: end;">
                        <button id="" data-toggle="modal" data-target="#bookingfinal" class="btn btn-warning">Book
                            now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bookingfinal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content"
                style="background-color: #a897c9f2!important; top: 300px; width: 700px; right: -250px!important; justify-content: space-between;">
                <div class="modal-body" style="margin: 20px!important; ">
                    <div class="row justifycontentspacearound">
                        <h3>Are you sure this point?</h3>
                    </div>
                    <!-- <div class="row" style="display: block;">
                        <h5 style="color: grey!important;">Doctor: </h5>
                        <h5 style="color: grey!important;">Patient: </h5>
                        <h5 style="color: grey!important;">When: </h5>
                        <h5 style="color: grey!important;">Where: </h5>
                    </div> -->
                    <div class="row" style="justify-content: center;">
                        <button type="submit" id="bookingbutton" class="btn btn-danger margin5">Confirm</button>
                        <button type="button" class="btn btn-secondary margin5" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let final_time = '';
    let final_doctor_id = '';
    let final_patient_id = '';
    let final_address_id = '';

    function opendoctordetail($id) {
        $.ajax({
            url: '/openbooking',
            method: 'post',
            data: {
                doctor_id: $id.split(':')[0],
                day: $id.split(':')[1],
                _token: "{{ csrf_token() }}"
            }
        }).done(function (res) {
            if (res.status == 'success') {
                toastr.success('You are available from ' + res.start + ' to ' + res.end, 'Booking Times');
                $('#bookingmodal').modal('show');
                let times = timeticket(res.start, res.end);

                $('#timesarea').empty();
                for (let i = 0; i < times.length; i++) {
                    let button = $('<button>').addClass('btn time yellow').text(times[i]);
                    button.css('margin', '5px');
                    button.click(function () {
                        final_time = $id.split(':')[2] + ' ' + times[i] + ":00";
                        final_doctor_id = $id.split(':')[0];
                        console.log('final doctor id', final_doctor_id)
                        console.log('final time', final_time)

                        $(this).removeClass('yellow').addClass('btn-secondary');
                        $('.btn.btn-secondary').not(this).not('#bookingbutton').not('.patient').not('.doctoraddress').not('.patientaddress').removeClass('btn-secondary').addClass('yellow');
                    });
                    $('#timesarea').append(button);
                }

                $('#patients').empty();
                for (let i = 0; i < res.patients.length; i++) {
                    let button = $('<button>').addClass('btn patient patient_color').text(res.patients[i].first_name + ' ' + res.patients[i].last_name);
                    button.css('margin', '5px');
                    button.click(function () {
                        final_patient_id = res.patients[i].id;
                        console.log('final patient id', final_patient_id);
                        $(this).removeClass('patient_color').addClass('btn-secondary');
                        $('.btn.btn-secondary').not(this).not('#bookingbutton').not('.time').not('.patientaddress').not('.doctoraddress').removeClass('btn-secondary').addClass('patient_color');
                    });
                    $('#patients').append(button);
                }

                $('#doctoraddress').empty();
                for (let i = 0; i < res.doctor_address.length; i++) {
                    let button = $('<button>').addClass('btn doctoraddress patient_color').text(res.doctor_address[i].description + ': ' + res.doctor_address[i].address);
                    button.css('margin', '5px');
                    button.click(function () {
                        final_address_id = res.doctor_address[i].id;
                        console.log('final address id', final_address_id);
                        $(this).removeClass('patient_color').addClass('btn-secondary');
                        $('.btn.btn-secondary').not(this).not('#bookingbutton').not('.time').not('.patient').removeClass('btn-secondary').addClass('patient_color');
                    });
                    $('#doctoraddress').append(button);
                }

                $('#patientaddress').empty();
                for (let i = 0; i < res.patient_address.length; i++) {
                    let button = $('<button>').addClass('btn patientaddress patient_color').text(res.patient_address[i].description + ': ' + res.patient_address[i].address);
                    button.css('margin', '5px');
                    button.click(function () {
                        final_address_id = res.patient_address[i].id;
                        console.log('final address id', final_address_id);
                        $(this).removeClass('patient_color').addClass('btn-secondary');
                        $('.btn.btn-secondary').not(this).not('#bookingbutton').not('.time').not('.patient').removeClass('btn-secondary').addClass('patient_color');
                    });
                    $('#patientaddress').append(button);
                }

                $('#doctorImage').attr('src', res.doctor_image);
                $('#doctorname').html(res.doctor.name.split('"')[3]);
            } else {
                toastr.warning('Not available on this date.')
            }
        });
    }


    function timeticket(start, end) {
        const timePoints = [];
        const step = 15; // Step time value in minutes

        const startTime = new Date(`1970-01-01T${start}`);
        const endTime = new Date(`1970-01-01T${end}`);

        const roundedStartMinutes = Math.ceil(startTime.getMinutes() / step) * step;
        startTime.setMinutes(roundedStartMinutes);

        let currentTime = startTime;
        while (currentTime <= endTime) {
            const minutes = currentTime.getMinutes();

            if (minutes % 15 === 0 && minutes <= 60) {
                const formattedTime = currentTime.toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: 'numeric',
                    minute: '2-digit'
                });

                timePoints.push(formattedTime);
            }
            currentTime.setMinutes(currentTime.getMinutes() + step);
        }
        return timePoints;
    }



    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#bookingbutton').click(function () {
            if (!final_address_id | !final_doctor_id | !final_patient_id | !final_time) {
                toastr.warning('Data field empty. Try input')
            } else {
                $.ajax({
                    url: '/booknow',
                    method: 'post',
                    data: {
                        time: final_time,
                        doctor_id: final_doctor_id,
                        patient_id: final_patient_id,
                        address_id: final_address_id,
                        hint: $('#hint').val(),
                        _token: "{{ csrf_token() }}"
                    }
                }).done(function (res) {
                    if (res.status == "success") {
                        final_time = '';
                        final_doctor_id = '';
                        final_patient_id = '';
                        final_address_id = '';
                        toastr.success('You have successfully booked', "Success");
                        $('#bookingfinal').modal('hide');
                        $('#bookingmodal').modal('hide');

                    } else {
                        final_time = '';
                        final_doctor_id = '';
                        final_patient_id = '';
                        final_address_id = '';
                        toastr.error("The reservation could not be made due to an error", "Error");
                    }
                })
            }

        });

    });

</script>


<style>
    .homeBody {
        padding-top: 20px;
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
        padding-left: 100px;
        padding-right: 100px;
    }

    .card {
        width: 100%;
    }

    .card-img-top {
        width: 100%;
        height: auto;
        border-radius: 100px !important;
    }

    .datearea {
        background-color: yellow;
        width: 100%;
        height: 60px;
    }

    .margin-bottom-10 {
        text-align: center;
        margin-bottom: 12px;
    }

    .date {
        padding-top: 8px;
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

    .width100 {
        width: 100%;
    }

    .grey {
        color: grey;
    }

    .peachpuff {
        color: peachpuff;
    }

    #timesarea {
        margin: 10px 0;
    }

    .black {
        color: black;
        color: darkgoldenrod;
    }

    .yellow {
        border-radius: 0px !important;
        background: transparent !important;
        color: white!important;
    }

    .patient_color {
        border-radius: 0px !important;
        background: transparent !important;
        color: white!important;
    }

    .modal .modal-dialog {
        max-width: 1200px;
        width: 100%;
    }

    .modal-body {
        margin: 50px;
    }

    .bold {
        font-weight: 900;
        color: antiquewhite;
    }

    .blue {
        color: rgb(103, 153, 194);
    }

    .card-body {
        background: #ffffd9;
    }

    .modal-content {
        background-color: #000000ab !important;
    }

    .modal-header {
        border-bottom: 0px !important;
    }

    h5 {
        color: antiquewhite !important;
    }

    .margin5 {
        margin: 5px;
    }
</style>