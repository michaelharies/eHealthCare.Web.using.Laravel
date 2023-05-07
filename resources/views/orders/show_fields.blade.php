<!-- Id Field -->
<div class="form-group row col-md-4 col-sm-12">
    {!! Form::label('id', trans('lang.appointment_id'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>#{!! $appointment->id !!}</p>
  </div>

    {!! Form::label('appointment_customer', trans('lang.appointment_customer'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $appointment->user->name !!}</p>
  </div>

    {!! Form::label('appointment_customer_phone', trans('lang.appointment_customer_phone'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! isset($appointment->user->custom_fields['phone']) ? $appointment->user->custom_fields['phone']['view'] : "" !!}</p>
  </div>

    {!! Form::label('delivery_address', trans('lang.delivery_address'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $appointment->deliveryAddress ? $appointment->deliveryAddress->address : '' !!}</p>
  </div>

    {!! Form::label('appointment_date', trans('lang.appointment_date'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $appointment->created_at !!}</p>
  </div>


</div>

<!-- Appointment Status Id Field -->
<div class="form-group row col-md-4 col-sm-12">
    {!! Form::label('appointment_status_id', trans('lang.appointment_status_status'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $appointment->appointmentStatus->status  !!}</p>
  </div>

    {!! Form::label('active', trans('lang.appointment_active'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    @if($appointment->active)
      <p><span class='badge badge-success'> {{trans('lang.yes')}}</span></p>
      @else
      <p><span class='badge badge-danger'>{{trans('lang.appointment_canceled')}}</span></p>
      @endif
  </div>

    {!! Form::label('payment_method', trans('lang.payment_method'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! isset($appointment->payment) ? $appointment->payment->method : ''  !!}</p>
  </div>

    {!! Form::label('payment_status', trans('lang.payment_status'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! isset($appointment->payment) ? $appointment->payment->status : trans('lang.appointment_not_paid')  !!}</p>
  </div>
    {!! Form::label('appointment_updated_date', trans('lang.appointment_updated_at'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        <p>{!! $appointment->updated_at !!}</p>
    </div>

</div>

<!-- Id Field -->
<div class="form-group row col-md-4 col-sm-12">
    {!! Form::label('clinic', trans('lang.clinic'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($appointment->doctorAppointments[0]))
            <p>{!! $appointment->doctorAppointments[0]->doctor->clinic->name !!}</p>
        @endif
    </div>

    {!! Form::label('eprovider_address', trans('lang.eprovider_address'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($appointment->doctorAppointments[0]))
            <p>{!! $appointment->doctorAppointments[0]->doctor->clinic->address !!}</p>
        @endif
    </div>

    {!! Form::label('eprovider_phone', trans('lang.eprovider_phone'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($appointment->doctorAppointments[0]))
            <p>{!! $appointment->doctorAppointments[0]->doctor->clinic->phone !!}</p>
        @endif
    </div>

    {!! Form::label('driver', trans('lang.driver'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($appointment->driver))
            <p>{!! $appointment->driver->name !!}</p>
        @else
            <p>{{trans('lang.appointment_driver_not_assigned')}}</p>
        @endif

    </div>

    {!! Form::label('hint', 'Hint:', ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        <p>{!! $appointment->hint !!}</p>
    </div>

</div>

{{--<!-- Tax Field -->--}}
{{--<div class="form-group row col-md-6 col-sm-12">--}}
{{--  {!! Form::label('tax', 'Tax:', ['class' => 'col-4 control-label']) !!}--}}
{{--  <div class="col-8">--}}
{{--    <p>{!! $appointment->tax !!}</p>--}}
{{--  </div>--}}
{{--</div>--}}


