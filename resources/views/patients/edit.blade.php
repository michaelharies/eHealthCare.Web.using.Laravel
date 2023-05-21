@extends('layouts.app')
@push('css_lib')
<link rel="stylesheet" href="{{asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('vendor/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('vendor/summernote/summernote-bs4.min.css')}}">
<link rel="stylesheet" href="{{asset('vendor/dropzone/min/dropzone.min.css')}}">
@endpush
@section('content')
<div class="content">
  @include('flash::message')
  @include('adminlte-templates::common.errors')
  <div class="clearfix"></div>
  <div class="card shadow-sm">
    <div class="card-header">
      <ul class="nav nav-tabs d-flex flex-row align-items-start card-header-tabs">
        @can('patients.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('patients.index') !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.patient_table')}}</a>
        </li>
        @endcan
        @can('patients.create')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('patients.create') !!}"><i class="fas fa-plus mr-2"></i>{{trans('lang.patient_create')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-edit mr-2"></i>{{trans('lang.patient_edit')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      {!! Form::model($patient, ['route' => ['patients.update', $patient->id], 'method' => 'patch']) !!}
      <div class="row">
          @include('patients.fields')
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
<script src="{{asset('vendor/dropzone/min/dropzone.min.js')}}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    var dropzoneFields = [];
</script>
@endpush
