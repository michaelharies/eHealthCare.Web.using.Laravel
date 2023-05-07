<div class='btn-group btn-group-sm'>
    @can('doctorPatients.show')
    <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('doctorPatients.show', $id) }}" class='btn btn-link'>
        <i class="fas fa-eye"></i> </a> @endcan

    @can('doctorPatients.edit')
    <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.doctor_patients_edit')}}" href="{{ route('doctorPatients.edit', $id) }}" class='btn btn-link'>
        <i class="fas fa-edit"></i> </a> @endcan

    @can('doctorPatients.destroy') {!! Form::open(['route' => ['doctorPatients.destroy', $id], 'method' => 'delete']) !!} {!! Form::button('<i class="fas fa-trash"></i>', [ 'type' => 'submit', 'class' => 'btn btn-link text-danger', 'onclick' => "return confirm('Are you sure?')" ]) !!} {!! Form::close() !!} @endcan
</div>
