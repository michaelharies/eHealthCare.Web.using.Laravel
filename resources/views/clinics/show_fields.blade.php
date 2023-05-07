<!-- Id Field -->
<div class="form-group row col-6">
    {!! Form::label('id', 'Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->id !!}</p>
    </div>
</div>

<!-- Image Field -->
<div class="form-group row col-6">
    {!! Form::label('image', 'Image:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->image !!}</p>
    </div>
</div>

<!-- Name Field -->
<div class="form-group row col-6">
    {!! Form::label('name', 'Name:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->name !!}</p>
    </div>
</div>
<!-- Users Field -->
<div class="form-group row col-6">
    {!! Form::label('users', 'Users:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->users !!}</p>
    </div>
</div>

<!-- Clinic Level Id Field -->
<div class="form-group row col-6">
    {!! Form::label('clinic_level_id', 'Clinic Level Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->clinic_level_id !!}</p>
    </div>
</div>

<!-- Description Field -->
<div class="form-group row col-6">
    {!! Form::label('description', 'Description:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->description !!}</p>
    </div>
</div>

<!-- Phone Number Field -->
<div class="form-group row col-6">
    {!! Form::label('phone_number', 'Phone Number:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->phone_number !!}</p>
    </div>
</div>

<!-- Mobile Number Field -->
<div class="form-group row col-6">
    {!! Form::label('mobile_number', 'Mobile Number:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->mobile_number !!}</p>
    </div>
</div>

<!-- Addresses Field -->
<div class="form-group row col-6">
    {!! Form::label('addresses', 'Addresses:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->address !!}</p>
    </div>
</div>

<!-- Availability Range Field -->
<div class="form-group row col-6">
    {!! Form::label('availability_range', 'Availability Range:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->availability_range !!}</p>
    </div>
</div>

<!-- Available Field -->
<div class="form-group row col-6">
    {!! Form::label('available', 'Available:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->available !!}</p>
    </div>
</div>

<!-- Taxes Field -->
<div class="form-group row col-6">
    {!! Form::label('taxes', 'Taxes:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->taxes !!}</p>
    </div>
</div>

<!-- Featured Field -->
<div class="form-group row col-6">
    {!! Form::label('featured', 'Featured:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $clinic->featured !!}</p>
    </div>
</div>

