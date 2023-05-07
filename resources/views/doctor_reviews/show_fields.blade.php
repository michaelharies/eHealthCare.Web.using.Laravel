<!-- Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('id', 'Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->id !!}</p>
    </div>
</div>

<!-- Review Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('review', 'Review:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->review !!}</p>
    </div>
</div>

<!-- Rate Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('rate', 'Rate:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->rate !!}</p>
    </div>
</div>

<!-- User Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('user_id', 'User Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->user_id !!}</p>
    </div>
</div>

<!-- E Service Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('doctor_id', 'E Service Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->doctor_id !!}</p>
    </div>
</div>

<!-- Created At Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('created_at', 'Created At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->created_at !!}</p>
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorReview->updated_at !!}</p>
    </div>
</div>


