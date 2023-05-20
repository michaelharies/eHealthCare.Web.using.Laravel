<div class='btn-group btn-group-sm'>
    @can('doctors.show')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('doctors.show', $id) }}" class='btn btn-link'>
            <i class="fas fa-eye"></i> </a> @endcan
 
    @can('doctors.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.doctor_edit')}}" href="{{ route('doctors.edit', $id) }}" class='btn btn-link'>
            <i class="fas fa-edit"></i> </a> @endcan

    @can('doctors.destroy') {!! Form::open(['route' => ['doctors.destroy', $id], 'method' => 'delete']) !!} {!! Form::button('<i class="fas fa-trash"></i>', [ 'type' => 'submit', 'class' => 'btn btn-link text-danger', 'onclick' => "return confirm('Are you sure?')" ]) !!} {!! Form::close() !!} @endcan
</div>
