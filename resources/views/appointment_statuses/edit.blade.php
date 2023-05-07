@extends('layouts.app')
@push('css_lib')

@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6">
                    <h1 class="m-0 text-bold">{{trans('lang.appointment_status_plural')}}
                        <small class="mx-3">|</small><small>{{trans('lang.appointment_status_desc')}}</small></h1>
                </div><!-- /.col -->
                <div class="col-md-6">
                    <ol class="breadcrumb bg-white float-sm-right rounded-pill px-4 py-2 d-none d-md-flex">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fas fa-tachometer-alt mx-1"></i> {{trans('lang.dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{!! route('appointmentStatuses.index') !!}">{{trans('lang.appointment_status_plural')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans('lang.appointment_status_edit')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        @include('adminlte-templates::common.errors')
        <div class="clearfix"></div>
        <div class="card shadow-sm">
            <div class="card-header">
                <ul class="nav nav-tabs d-flex flex-row align-items-start card-header-tabs">
                    @can('appointmentStatuses.index')
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('appointmentStatuses.index') !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.appointment_status_table')}}
                            </a>
                        </li>
                    @endcan
                    @can('appointmentStatuses.create')
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('appointmentStatuses.create') !!}"><i class="fas fa-plus mr-2"></i>{{trans('lang.appointment_status_create')}}
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-edit mr-2"></i>{{trans('lang.appointment_status_edit')}}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                {!! Form::model($appointmentStatus, ['route' => ['appointmentStatuses.update', $appointmentStatus->id], 'method' => 'patch']) !!}
                <div class="row">
                    @include('appointment_statuses.fields')
                </div>
                {!! Form::close() !!}
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection
@push('scripts_lib')
@endpush
