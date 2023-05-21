@extends('layouts.app')

@section('content')

<div class="content">
  <div class="card shadow-sm">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link" href="{!! route('patients.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.patient_table')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{!! route('patients.create') !!}"><i class="fas fa-plus mr-2"></i>{{trans('lang.patient_create')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="row">
        @include('patients.show_fields')

        <!-- Back Field -->
        <div class="form-group col-12 text-md-right">
          <a href="{!! route('patients.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.back')}}</a>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
@endsection
