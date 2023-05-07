<div class='btn-group btn-group-sm'>
    @can('earnings.create')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.clinic_payout_create')}}" href="{{ isset($clinic_id) ? route('clinicPayouts.create', $clinic_id ) : "#" }}" class='btn btn-link'>
            <i class="fas fa-money-bill-wave"></i> </a>
    @endcan

</div>
