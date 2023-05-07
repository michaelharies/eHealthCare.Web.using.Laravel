@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold">{{trans('lang.appointment_plural')}}<small class="mx-3">|</small><small>{{trans('lang.appointment_desc')}}</small></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white float-sm-right rounded-pill px-4 py-2 d-none d-md-flex">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fas fa-tachometer-alt"></i> {{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('appointments.index') !!}">{{trans('lang.appointment_plural')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans('lang.appointment')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="content">
        <div class="card shadow-sm">
            <div class="card-header d-print-none">
                <ul class="nav nav-tabs d-flex flex-row align-items-start card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{!! route('appointments.index') !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.appointment_table')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-plus mr-2"></i>{{trans('lang.appointment')}}</a>
                    </li>
                    <div class="ml-auto d-inline-flex">
                        <li class="nav-item">
                            <a class="nav-link pt-1" id="printAppointment" href="#"><i class="fas fa-print"></i> {{trans('lang.print')}}</a>
                        </li>
                    </div>
                </ul>
            </div>
            <div class="card-body">
                <div class="row">
                    @include('appointments.show_fields')
                </div>
                @include('doctor_appointments.table')
                <div class="row">
      <div class="col-5 offset-7">
        <div class="table-responsive table-light">
          <table class="table">
            <tbody><tr>
              <th class="text-right">{{trans('lang.appointment_subtotal')}}</th>
              <td>{!! getPrice($subtotal) !!}</td>
            </tr>
            <tr>
              <th class="text-right">{{trans('lang.appointment_additional_fee')}}</th>
              <td>{!! getPrice($appointment['additional_fee'])!!}</td>
            </tr>
            <tr>
              <th class="text-right">{{trans('lang.appointment_tax')}} ({!!$appointment->tax!!}%) </th>
              <td>{!! getPrice($taxAmount)!!}</td>
            </tr>

            <tr>
              <th class="text-right">{{trans('lang.appointment_total')}}</th>
                <td>{!!getPrice($total)!!}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
                </div>
                <div class="clearfix"></div>
                <div class="row d-print-none">
                    <!-- Back Field -->
                    <div class="form-group col-12 d-flex flex-column flex-md-row justify-content-md-end justify-content-sm-center border-top pt-4">
                        <a href="{!! route('appointments.index') !!}" class="btn btn-default"><i class="fas fa-undo"></i> {{trans('lang.back')}}</a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
    $("#printAppointment").on("click",function () {
      window.print();
    });
  </script>
@endpush
