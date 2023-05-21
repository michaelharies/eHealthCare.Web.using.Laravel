@extends('layouts.app')
@push('css_lib')
    <link rel="stylesheet" href="{{asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/summernote/summernote-bs4.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
@endpush
@section('content')

    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        @include('adminlte-templates::common.errors')
        <div class="clearfix"></div>
        <div class="card shadow-sm">
            <div class="card-header">
                <ul class="nav nav-tabs d-flex flex-row align-items-start card-header-tabs">
                    @can('appointments.index')
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('appointments.index') !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.appointment_table')}}</a>
                        </li>
                    @endcan
                    @can('appointments.show')
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('appointments.show',$appointment->id) !!}"><i class="fas fa-calendar-check mr-2"></i>{{trans('lang.appointment_details')}}
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-edit mr-2"></i>{{trans('lang.appointment_edit')}}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                {!! Form::model($appointment, ['route' => ['appointments.update', $appointment->id], 'method' => 'patch']) !!}
                <div class="row">
                    @include('appointments.fields')
                </div>
                {!! Form::close() !!}
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    @include('layouts.media_modal')
@endsection
@push('scripts_lib')
    <script src="{{asset('vendor/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('vendor/summernote/summernote.min.js')}}"></script>
    <script src="{{asset('vendor/moment/moment.min.js')}}"></script>
    <script src="{{asset('vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
@endpush
