@extends('layouts.app')
@push('css_lib')
    <link rel="stylesheet" href="{{asset('vendor/bs-stepper/css/bs-stepper.min.css')}}">
@endpush
@section('content')

    <div class="content d-flex flex-column flex-md-row">
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-header">
                    <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('appointments.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.appointment_table')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{!! route('appointments.show',$appointment->id) !!}"><i class="fas fa-calendar-check mr-2"></i>{{trans('lang.appointment_details')}}
                            </a>
                        </li>
                        @can('appointments.edit')
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('appointments.edit',$appointment->id) !!}"><i class="fas fa-edit mr-2"></i>{{trans('lang.appointment_edit')}}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
                <div class="card-body p-0">

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            <b>{{__('lang.appointment_status')}}</b>
                            @if($appointment->cancel)
                                <span class="badge bg-danger px-2 py-2">{{__('lang.appointment_cancel')}}</span>
                            @endif
                        </li>
                        <li class="bs-stepper list-group-item">
                            <div class="bs-stepper-header" role="tablist">
                                @foreach($appointmentStatuses as $appointmentStatus)
                                    <div class="step">
                                        <span role="tab">
                                            <span class="bs-stepper-circle @if($appointmentStatus->id == $appointment->appointment_status_id) bg-{{setting('theme_color')}} @endif">{{$appointmentStatus->order}}</span>
                                            <span class="bs-stepper-label">{{$appointmentStatus->status}}</span> </span>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="line"></div>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                        <li class="list-group-item bg-light">
                            <b>{{__('lang.appointment_id')}} #{{$appointment->id}}</b>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {!! getMediaColumn($appointment->doctor,'image','rounded shadow-sm border') !!}
                            <div class="d-flex flex-column mx-3">
                                <small>{{__('lang.appointment_doctor')}}</small>
                                <span><b>{{$appointment->doctor->name}}</b><small class="mx-3">{{__('lang.by')}} {{$appointment->clinic->name}}</small></span>
                            </div>
                            <div class="text-bold ml-xl-auto my-1 my-xl-0">
                                @if($appointment->doctor->hasDiscount())
                                    <del class="text-gray">{!! getPrice($appointment->doctor->price) !!}</del>
                                @else
                                    <span class="h5 text-bold">{!! getPrice($appointment->doctor->getPrice()) !!} </span>
                                @endif
                            </div>
                        </li>
                    </ul>
                    <div class="d-flex flex-column flex-md-row">
                        <ul class="list-group list-group-flush col-12 col-lg-7 p-0">
                            <span class="list-group-item py-0"></span>
                            <li class="list-group-item bg-light">
                                <b>{{__('lang.payment')}}</b>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.payment_status')}}</b>
                                <small class="badge badge-light px-2 py-1">{{empty(!$appointment->payment) ? $appointment->payment->paymentStatus->status : '-'}}</small>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.payment_method')}}</b>
                                <small class="badge badge-light px-2 py-1">{{empty(!$appointment->payment) ? $appointment->payment->paymentMethod->name : '-'}}</small>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.appointment_hint')}}</b> <small>{{$appointment->hint}}</small>
                            </li>
                        </ul>
                        <ul class="list-group list-group-flush col-12 col-lg-5 p-0">
                            <span class="list-group-item py-0"></span>
                            <li class="list-group-item bg-light">
                                <b>{{__('lang.appointment_taxes_fees')}}</b>
                            </li>
                            @foreach($appointment->taxes as $tax)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><b>{{$tax->name}}</b></span>
                                    <h6 class="text-bold ml-xl-auto my-1 my-xl-0">
                                        @if($tax->type == 'percent')
                                            {{$tax->value .'%'}}
                                        @else
                                            {!! getPriceColumn($tax,'value') !!}
                                        @endif
                                    </h6>
                                </li>
                            @endforeach
                            <li class="list-group-item bg-light">
                                <b>{{__('lang.appointment_coupon')}}</b>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><b>{{$appointment->coupon->code}}</b><small class="mx-3"> {{getStripedHtmlColumn($appointment->coupon,'description')}}</small></span>
                                <h6 class="text-bold ml-xl-auto my-1 my-xl-0">
                                    @if($appointment->coupon->discount_type == 'percent')
                                        {{(-$appointment->coupon->discount) .'%'}}
                                    @else
                                        {!! getPrice(-$appointment->coupon->discount) !!}
                                    @endif
                                </h6>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.appointment_subtotal')}}</b> <h6 class="text-bold">{!! getPrice($appointment->getSubtotal()) !!}</h6>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.appointment_total')}}</b> <h5 class="text-bold">{!! getPrice($appointment->getTotal()) !!}</h5>
                            </li>
                        </ul>
                    </div>

                    <!-- Back Field -->
                    <div class="form-group col-12 d-flex flex-column flex-md-row justify-content-md-end justify-content-sm-center border-top pt-4">
                        <a href="#" class="btn btn-default mx-md-2 my-md-0 my-2" id="printOrder"> <i class="fas fa-print"></i> {{trans('lang.print')}}
                        </a> 
                        <!-- <a href="{!! route('appointments.edit', $appointment->id) !!}" class="btn btn-default mx-md-2">
                            <i class="fas fa-edit"></i> {{trans('lang.appointment_edit')}}
                        </a>  -->
                        <a href="{!! route('appointments.index') !!}" class="btn btn-default mx-md-2">
                            <i class="fas fa-list"></i> {{trans('lang.appointment_table')}}
                        </a>

                    </div>

                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    {{__('lang.appointment_user_id')}}
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex flex-xl-row flex-column justify-content-between align-items-center align-items-xl-start px-0">
                            {!! getMediaColumn($appointment->patient,'avatar','img-circle shadow-sm border') !!}
                            <div class="d-flex flex-column align-items-center align-items-xl-start mx-2 my-1 my-xl-0 my-0">
                                <b>{{$appointment->patient->first_name}}</b><b>{{$appointment->patient->last_name}}</b> <small>{{$appointment->patient->phone_number}}</small>
                            </div>
                            <a target="_blank" class="btn btn-sm btn-default ml-xl-auto my-1 my-xl-0" href="{{route('users.edit',$appointment->user->id)}}"><i class="fas fa-user-alt mx-1"></i>{{__('lang.user_profile')}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    {{__('lang.appointment_time')}}
                </div>
                <div class="card-body px-0 py-1">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.appointment_appointment_at')}}</b> <small>{{$appointment->appointment_at}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.appointment_start_at')}}</b> <small>{{$appointment->start_at ?: '-'}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.appointment_ends_at')}}</b> <small>{{$appointment->ends_at ?: '-'}}</small>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    {{__('lang.appointment_address')}}
                </div>
                <div class="card-body  px-0 py-1">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <small>{{$appointment->address->address}}</small>
                        </li>
                        <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.address_open_with')}}</b>
                            <a target="_blank" class="btn btn-sm btn-default" href="{{'https://www.google.com/maps/@'.$appointment->address->latitude .','.$appointment->address->longitude.',14z'}}"><i class="fas fa-directions mx-1"></i>{{__('lang.address_google_maps')}}
                            </a>
                        </li> -->
                    </ul>
                </div>
            </div>

        </div>

    </div>
    <!-- /.modal -->
@endsection
@push('scripts_lib')
    {{--    <script src="{{asset('vendor/bs-stepper/js/bs-stepper.min.js')}}"></script>--}}
@endpush
@push('scripts')
    <script type="text/javascript">
        $("#printOrder").on("click",function () {
            window.print();
        });
    </script>
@endpush