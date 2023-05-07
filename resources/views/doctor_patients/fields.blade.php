@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-6">
<!-- Doctor Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('doctor_id', trans("lang.doctor_patients_doctor_id"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        {!! Form::number('doctor_id', null, ['class' => 'form-control','step'=>'any', 'min'=>'0', 'placeholder'=> trans("lang.doctor_patients_doctor_id_placeholder")]) !!}
        <div class="form-text text-muted">
            {{ trans("lang.doctor_patients_doctor_id_help") }}
        </div>
    </div>
</div>

</div>
<div class="d-flex flex-column col-sm-12 col-md-6">

<!-- Patient Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('patient_id', trans("lang.doctor_patients_patient_id"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        {!! Form::number('patient_id', null, ['class' => 'form-control','step'=>'any', 'min'=>'0', 'placeholder'=> trans("lang.doctor_patients_patient_id_placeholder")]) !!}
        <div class="form-text text-muted">
            {{ trans("lang.doctor_patients_patient_id_help") }}
        </div>
    </div>
</div>

</div>
@if($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
  <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
  {!! $customFields !!}
</div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 d-flex flex-column flex-md-row justify-content-md-end justify-content-sm-center border-top pt-4">
  <button type="submit" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2" ><i class="fas fa-save"></i> {{trans('lang.save')}} {{trans('lang.doctor_patients')}}</button>
  <a href="{!! route('doctorPatients.index') !!}" class="btn btn-default"><i class="fas fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
