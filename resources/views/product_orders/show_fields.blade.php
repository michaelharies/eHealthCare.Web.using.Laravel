<!-- Id Field -->
<div class="form-group row col-6">
    {!! Form::label('id', 'Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->id !!}</p>
    </div>
</div>

<!-- Price Field -->
<div class="form-group row col-6">
    {!! Form::label('price', 'Price:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->price !!}</p>
    </div>
</div>

<!-- Quantity Field -->
<div class="form-group row col-6">
    {!! Form::label('quantity', 'Quantity:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->quantity !!}</p>
    </div>
</div>

<!-- Doctor Id Field -->
<div class="form-group row col-6">
    {!! Form::label('doctor_id', 'Doctor Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->doctor_id !!}</p>
    </div>
</div>

<!-- Options Field -->
<div class="form-group row col-6">
    {!! Form::label('options', 'Options:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->options !!}</p>
    </div>
</div>

<!-- Appointment Id Field -->
<div class="form-group row col-6">
    {!! Form::label('appointment_id', 'Appointment Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->appointment_id !!}</p>
    </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
    {!! Form::label('created_at', 'Created At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->created_at !!}</p>
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $doctorAppointment->updated_at !!}</p>
    </div>
</div>

