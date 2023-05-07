<aside class="main-sidebar sidebar-{{setting('theme_contrast')}}-{{setting('theme_color')}} shadow">
    <a href="{{url('dashboard')}}"  class="brand-link border-bottom-0 text-light navbar-yellow {{setting('bg-white')}}" style="background: yellow">
        <img src="{{$app_logo ?? ''}}" alt="{{setting('app_name')}}"  class="brand-image" style="margin-left: 1.4rem">

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                @include('layouts.menu',['icons'=>true])
            </ul>
        </nav>
    </div>
</aside>
