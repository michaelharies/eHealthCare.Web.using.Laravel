<div class='btn-group btn-group-sm'>
    @can('doctorAppointments.show')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('doctorAppointments.show', $id) }}" class='btn btn-link'>
            <i class="fas fa-eye"></i> </a>
    @endcan

    @can('doctorAppointments.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.doctor_appointment_edit')}}" href="{{ route('doctorAppointments.edit', $id) }}" class='btn btn-link'>
            <i class="fas fa-edit"></i> </a>
    @endcan

    @can('appointments.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.appointment_edit')}}" href="{{ route('appointments.edit', $appointment['id']) }}" class='btn btn-link'>
            <i class="fas fa-tasks"></i> </a>
    @endcan

    @can('doctorAppointments.destroy')
        {!! Form::open(['route' => ['doctorAppointments.destroy', $id], 'method' => 'delete']) !!}
        {!! Form::button('<i class="fas fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-link text-danger',
        'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        {!! Form::close() !!}
  @endcan
</div>
