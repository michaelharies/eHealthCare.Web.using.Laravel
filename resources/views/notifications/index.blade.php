@extends('layouts.app')

@section('content')


    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <div class="card shadow-sm">
            <div class="card-header">
                <ul class="nav nav-tabs d-flex flex-md-row flex-column-reverse align-items-start card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.notification_table')}}</a>
                    </li>
                    @can('notifications.create')
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('notifications.create') !!}"><i class="fas fa-plus mr-2"></i>{{trans('lang.notification_create')}}
                            </a>
                        </li>
                    @endcan
                    @include('layouts.right_toolbar', compact('dataTable'))
                </ul>
            </div>
            <div class="card-body">
                @include('notifications.table')
                <div class="clearfix"></div>
            </div>
        </div>
</div>
@endsection

