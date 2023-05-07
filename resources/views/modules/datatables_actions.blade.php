<div class='d-inline-flex'>
    @if($installed)
        {!! Form::open(['route' => ['modules.enable', $id], 'method' => 'put']) !!}
        {!! Form::button($enabled ? trans('lang.module_disable') : trans('lang.module_enable'), [
        'type' => 'submit',
        'class' => 'mx-1 btn btn-outline-primary btn-sm',
        'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        {!! Form::close() !!}
    @endif
    @if(!$installed)
        {!! Form::open(['route' => ['modules.install', $id], 'method' => 'post']) !!}
        {!! Form::button(trans('lang.module_install'), [
        'type' => 'submit',
        'class' => 'mx-1 btn btn-outline-primary btn-sm',
        'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        {!! Form::close() !!}
    @endif
    @if(!$updated)
        {!! Form::open(['route' => ['modules.update', $id], 'method' => 'post']) !!}
        {!! Form::button(trans('lang.module_update'), [
        'type' => 'submit',
        'class' => 'mx-1 btn btn-outline-primary btn-sm',
        'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        {!! Form::close() !!}
    @endif
    <a target="_blank" data-toggle="tooltip" data-placement="left" title="{{trans('lang.module_buy')}}" href="{{ url($url) }}" class=' mx-1 btn btn-success btn-sm'>
        <i class="fas fa-shopping-cart"></i> {{trans('lang.module_buy')}}
    </a>
</div>
