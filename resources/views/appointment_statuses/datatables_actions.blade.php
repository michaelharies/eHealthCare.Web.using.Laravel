<div class='btn-group btn-group-sm'>
    @can('appointmentStatuses.show')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('appointmentStatuses.show', $id) }}" class='btn btn-link'>
            <i class="fas fa-eye"></i> </a> @endcan

    @can('appointmentStatuses.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.appointment_status_edit')}}" href="{{ route('appointmentStatuses.edit', $id) }}" class='btn btn-link'>
            <i class="fas fa-edit"></i> </a> @endcan

    @can('appointmentStatuses.destroy') {!! Form::open(['route' => ['appointmentStatuses.destroy', $id], 'method' => 'delete']) !!} {!! Form::button('<i class="fas fa-trash"></i>', [ 'type' => 'submit', 'class' => 'btn btn-link text-danger', 'onclick' => "return confirm('Are you sure?')" ]) !!} {!! Form::close() !!} @endcan
</div>
