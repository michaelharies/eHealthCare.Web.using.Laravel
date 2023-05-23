@can('patients.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('patients*') ? 'active' : '' }}" href="{!! route('patients.index') !!}">@if($icons)<i class="nav-icon fas fa-procedures"></i>@endif<p>{{trans('lang.patient_plural')}}</p></a>
    </li>
@endcan
@can('appointments.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('appointments*') ? 'active' : '' }}" href="{!! route('appointments.index') !!}">@if($icons)
                <i class="nav-icon fas fa-calendar-check"></i>@endif<p>{{trans('lang.appointment_plural')}}</p></a>
    </li>
@endcan
@can('addresses.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('addresses*') ? 'active' : '' }}" href="{!! route('addresses.index') !!}">@if($icons)
                <i class="nav-icon fas fa-map-marked-alt"></i>@endif<p>{{trans('lang.address_plural')}}</p></a>
    </li>
@endcan
@can('faqs.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('faqs*') ? 'active' : '' }}" href="{!! route('faqs.index') !!}">@if($icons)
                <i class="nav-icon fas fa-life-ring"></i>@endif
            <p>{{trans('lang.faq_plural')}}</p></a>
    </li>
@endcan
