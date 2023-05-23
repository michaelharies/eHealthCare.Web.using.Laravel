<aside class="main-sidebar sidebar-{{setting('theme_contrast')}}-{{setting('theme_color')}} shadow">
    <a href="{{url('dashboard')}}" class="brand-link border-bottom-0 text-light navbar-yellow {{setting('bg-white')}}" style="background: yellow">
        <img src="{{$app_logo ?? ''}}" alt="{{setting('app_name')}}" class="brand-image" style="margin-left: 1.4rem">
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                @can('patients.index')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('patients*') ? 'active' : '' }}" href="{!! route('patients.index') !!}"><i class="nav-icon fas fa-procedures"></i><p>{{trans('lang.patient_plural')}}</p></a>
                    </li>
                @endcan
                @can('appointments.index')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('appointments*') ? 'active' : '' }}" href="{!! route('appointments.index') !!}">
                                <i class="nav-icon fas fa-calendar-check"></i><p>{{trans('lang.appointment_plural')}}</p></a>
                    </li>
                @endcan
                @can('addresses.index')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('addresses*') ? 'active' : '' }}" href="{!! route('addresses.index') !!}">
                                <i class="nav-icon fas fa-map-marked-alt"></i><p>{{trans('lang.address_plural')}}</p></a>
                    </li>
                @endcan
                @can('faqs.index')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('faqs*') ? 'active' : '' }}" href="{!! route('faqs.index') !!}">
                                <i class="nav-icon fas fa-life-ring"></i>
                            <p>{{trans('lang.faq_plural')}}</p></a>
                    </li>
                @endcan
            </ul>
        </nav>
    </div>
</aside>