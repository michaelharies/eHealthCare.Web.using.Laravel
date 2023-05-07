<div class='btn-group btn-group-sm'>
    @can('doctorReviews.show')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('doctorReviews.show', $id) }}" class='btn btn-link'>
            <i class="fas fa-eye"></i> </a> @endcan

    @can('doctorReviews.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.doctor_review_edit')}}" href="{{ route('doctorReviews.edit', $id) }}" class='btn btn-link'>
            <i class="fas fa-edit"></i> </a> @endcan

    @can('doctorReviews.destroy') {!! Form::open(['route' => ['doctorReviews.destroy', $id], 'method' => 'delete']) !!} {!! Form::button('<i class="fas fa-trash"></i>', [ 'type' => 'submit', 'class' => 'btn btn-link text-danger', 'onclick' => "return confirm('Are you sure?')" ]) !!} {!! Form::close() !!} @endcan
</div>
