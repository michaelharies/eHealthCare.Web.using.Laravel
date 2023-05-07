@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-6">

    <!-- Appointment Status Id Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('appointment_status_id', trans("lang.appointment_appointment_status_id"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('appointment_status_id', $appointmentStatus, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.appointment_appointment_status_id_help") }}</div>
        </div>
    </div>

    <!-- Address Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('address_id', trans("lang.appointment_address"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('address_id', $addresses, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.appointment_address_help") }}</div>
        </div>
    </div>

    <!-- Payment Status Id Field -->
    @if(!empty($paymentStatuses))
        <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
            {!! Form::label('payment_status_id', trans("lang.appointment_payment_id"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
            <div class="col-md-9">
                {!! Form::select('payment_status_id', $paymentStatuses, null, ['class' => 'select2 form-control']) !!}
                <div class="form-text text-muted">{{ trans("lang.appointment_payment_id_help") }}</div>
            </div>
        </div>
@endif

<!-- Hint Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('hint', trans("lang.appointment_hint"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::textarea('hint', null, ['class' => 'form-control','placeholder'=>
             trans("lang.appointment_hint_placeholder")  ]) !!}
            <div class="form-text text-muted">{{ trans("lang.appointment_hint_help") }}</div>
        </div>
    </div>

</div>
<div class="d-flex flex-column col-sm-12 col-md-6">

    <!-- Appointment At Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('appointment_at', trans("lang.appointment_appointment_at"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div class="input-group datepicker appointment_at" data-target-input="nearest">
                {!! Form::text('appointment_at', null,  ['class' => 'form-control datetimepicker-input','placeholder'=>  trans("lang.appointment_appointment_at_placeholder"), 'data-target'=>'.datepicker.appointment_at','data-toggle'=>'datetimepicker','autocomplete'=>'off']) !!}
                <div id="widgetParentId"></div>
                <div class="input-group-append" data-target=".datepicker.appointment_at" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-business-time"></i></div>
                </div>
            </div>
            <div class="form-text text-muted">
                {{ trans("lang.appointment_appointment_at_help") }}
            </div>
        </div>
    </div>

    <!-- Start At Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('start_at', trans("lang.appointment_start_at"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div class="input-group datepicker start_at" data-target-input="nearest">
                {!! Form::text('start_at', null,  ['class' => 'form-control datetimepicker-input','placeholder'=>  trans("lang.appointment_start_at_placeholder"), 'data-target'=>'.datepicker.start_at','data-toggle'=>'datetimepicker','autocomplete'=>'off']) !!}
                <div id="widgetParentId"></div>
                <div class="input-group-append" data-target=".datepicker.start_at" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-business-time"></i></div>
                </div>
            </div>
            <div class="form-text text-muted">
                {{ trans("lang.appointment_start_at_help") }}
            </div>
        </div>
    </div>

    <!-- Ends At Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('ends_at', trans("lang.appointment_ends_at"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div class="input-group datepicker ends_at" data-target-input="nearest">
                {!! Form::text('ends_at', null,  ['class' => 'form-control datetimepicker-input','placeholder'=>  trans("lang.appointment_ends_at_placeholder"), 'data-target'=>'.datepicker.ends_at','data-toggle'=>'datetimepicker','autocomplete'=>'off']) !!}
                <div id="widgetParentId"></div>
                <div class="input-group-append" data-target=".datepicker.ends_at" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-business-time"></i></div>
                </div>
            </div>
            <div class="form-text text-muted">
                {{ trans("lang.appointment_ends_at_help") }}
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
    <div class="d-flex flex-row justify-content-between align-items-center">
        {!! Form::label('cancel', trans("lang.appointment_cancel"),['class' => 'control-label my-0 mx-3']) !!} {!! Form::hidden('cancel', 0, ['id'=>"hidden_cancel"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('cancel', 1, null) !!} <label for="cancel"></label> </span>
    </div>

    <button type="submit" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2">
        <i class="fas fa-save"></i> {{trans('lang.save')}} {{trans('lang.appointment')}}</button>
    <a href="{!! route('appointments.index') !!}" class="btn btn-default"><i class="fas fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
