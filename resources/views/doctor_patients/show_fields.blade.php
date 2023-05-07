<!-- Doctor Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('doctor_id', 'Doctor Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    <p>{!! $doctorPatients->doctor_id !!}</p>
  </div>
</div>


<!-- Patient Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('patient_id', 'Patient Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    <p>{!! $doctorPatients->patient_id !!}</p>
  </div>
</div>


