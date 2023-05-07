@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-4 px-4">
    <!-- User Id Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('user_id', trans("lang.doctor_user_id"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('user_id', $user, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.doctor_user_id_help") }}</div>
        </div>
    </div>

    <!-- Name Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('name', trans("lang.patient_name"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.patient_name_help") }}
            </div>
        </div>
    </div>
    <!-- Specialities Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row ">
        {!! Form::label('specialities[]', trans("lang.doctor_specialities"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('specialities[]', $speciality, $specialitiesSelected, ['class' => 'select2 form-control not-required' , 'data-empty'=>trans('lang.doctor_specialities_placeholder'),'multiple'=>'multiple']) !!}
            <div class="form-text text-muted">{{ trans("lang.doctor_specialities_help") }}</div>
        </div>
    </div>

    <!-- Price Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row ">
        {!! Form::label('price', trans("lang.doctor_price"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div class="input-group">
                {!! Form::number('price', null, ['class' => 'form-control','step'=>'any', 'min'=>'0', 'placeholder'=> trans("lang.doctor_price_placeholder")]) !!}
                <div class="input-group-append">
                    <div class="input-group-text text-bold px-3">{{setting('default_currency','$')}}</div>
                </div>
            </div>
            <div class="form-text text-muted">
                {{ trans("lang.doctor_price_help") }}
            </div>
        </div>
    </div>

    <!-- Discount Price Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row ">
        {!! Form::label('discount_price', trans("lang.doctor_discount_price"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div class="input-group">
                {!! Form::number('discount_price', null, ['class' => 'form-control','step'=>'any', 'min'=>'0', 'placeholder'=> trans("lang.doctor_discount_price_placeholder")]) !!}
                <div class="input-group-append">
                    <div class="input-group-text text-bold px-3">{{setting('default_currency','$')}}</div>
                </div>
            </div>
            <div class="form-text text-muted">
                {{ trans("lang.doctor_discount_price_help") }}
            </div>
        </div>
    </div>

    <!-- Clinic Id Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row ">
        {!! Form::label('clinic_id', trans("lang.doctor_clinic_id"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('clinic_id', $clinic, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.doctor_clinic_id_help") }}</div>
        </div>
    </div>
    <!-- Commission Field -->
    @hasrole('admin')
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row ">
        {!! Form::label('commission', trans("lang.doctor_commission"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div class="input-group">
                {!! Form::number('commission', null, ['class' => 'form-control','step'=>'any', 'min'=>'0','max'=>'100', 'placeholder'=> trans("lang.doctor_commission_placeholder")]) !!}
                <div class="input-group-append">
                    <div class="input-group-text text-bold px-3">%</div>
                </div>
            </div>
            <div class="form-text text-muted">
                {{ trans("lang.doctor_commission_help") }}
            </div>
        </div>
    </div>
    @endhasrole
</div>
<div class="d-flex flex-column col-sm-12 col-md-4 px-4">

    <!-- Image Field -->
    <div class="form-group align-items-start d-flex flex-column flex-md-row">
        {!! Form::label('image', trans("lang.doctor_image"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
            </div>
            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
            <div class="form-text text-muted w-50">
                {{ trans("lang.doctor_image_help") }}
            </div>
        </div>
    </div>
    @prepend('scripts')
        <script type="text/javascript">
            var var16110647911349350349ble = [];
            @if(isset($doctor) && $doctor->hasMedia('image'))
            @forEach($doctor->getMedia('image') as $media)
            var16110647911349350349ble.push({
                name: "{!! $media->name !!}",
                size: "{!! $media->size !!}",
                type: "{!! $media->mime_type !!}",
                uuid: "{!! $media->getCustomProperty('uuid'); !!}",
                thumb: "{!! $media->getUrl('thumb'); !!}",
                collection_name: "{!! $media->collection_name !!}"
            });
            @endforeach
            @endif
            var dz_var16110647911349350349ble = $(".dropzone.image").dropzone({
                url: "{!!url('uploads/store')!!}",
                addRemoveLinks: true,
                maxFiles: 5 - var16110647911349350349ble.length,
                init: function () {
                    @if(isset($doctor) && $doctor->hasMedia('image'))
                    var16110647911349350349ble.forEach(media => {
                        dzInit(this, media, media.thumb);
                    });
                    @endif
                },
                accept: function (file, done) {
                    dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");
                },
                sending: function (file, xhr, formData) {
                    dzSendingMultiple(this, file, formData, '{!! csrf_token() !!}');
                },
                complete: function (file) {
                    dzCompleteMultiple(this, file);
                    dz_var16110647911349350349ble[0].mockFile = file;
                },
                removedfile: function (file) {
                    dzRemoveFileMultiple(
                        file, var16110647911349350349ble, '{!! url("doctors/remove-media") !!}',
                        'image', '{!! isset($doctor) ? $doctor->id : 0 !!}', '{!! url("uploads/clear") !!}', '{!! csrf_token() !!}'
                    );
                }
            });
            dz_var16110647911349350349ble[0].mockFile = var16110647911349350349ble;
            dropzoneFields['image'] = dz_var16110647911349350349ble;
        </script>
    @endprepend
    <!-- Description Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row ">
        {!! Form::label('description', trans("lang.doctor_description"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::textarea('description', null, ['class' => 'form-control','placeholder'=>
             trans("lang.doctor_description_placeholder")  ]) !!}
            <div class="form-text text-muted">{{ trans("lang.doctor_description_help") }}</div>
        </div>
    </div>

</div>
<div class="d-flex flex-column col-sm-12 col-md-4 px-4">
    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        {!! Form::label('featured', trans("lang.doctor_featured"),['class' => 'control-label my-0 mx-3']) !!} {!! Form::hidden('featured', 0, ['id'=>"hidden_featured"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('featured', 1, null) !!} <label for="featured"></label> </span>
    </div>
    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        {!!  Form::label('enable_appointment', trans("lang.doctor_enable_appointment"),['class' => 'control-label my-0 mx-3'], false)  !!} {!! Form::hidden('enable_appointment', 0, ['id'=>"hidden_enable_appointment"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('enable_appointment', 1, null) !!} <label for="enable_appointment"></label> </span>
    </div>
    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        {!!  Form::label('enable_at_clinic', trans("lang.doctor_enable_at_clinic"),['class' => 'control-label my-0 mx-3'], false)  !!} {!! Form::hidden('enable_at_clinic', 0, ['id'=>"hidden_enable_at_clinic"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('enable_at_clinic', 1, null) !!} <label for="enable_at_clinic"></label> </span>
    </div>
    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        {!!  Form::label('enable_at_customer_address', trans("lang.doctor_enable_at_customer_address"),['class' => 'control-label my-0 mx-3'], false)  !!} {!! Form::hidden('enable_at_customer_address', 0, ['id'=>"hidden_enable_at_customer_address"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('enable_at_customer_address', 1, null) !!} <label for="enable_at_customer_address"></label> </span>
    </div>
    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        {!!  Form::label('enable_online_consultation', trans("lang.doctor_enable_online_consultation"),['class' => 'control-label my-0 mx-3'], false)  !!} {!! Form::hidden('enable_online_consultation', 0, ['id'=>"hidden_enable_online_consultation"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('enable_online_consultation', 1, null) !!} <label for="enable_online_consultation"></label> </span>
    </div>
    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        {!! Form::label('available', trans("lang.doctor_available"),['class' => 'control-label my-0 mx-3']) !!} {!! Form::hidden('available', 0, ['id'=>"hidden_available"]) !!}
        <span class="icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('available', 1, null) !!} <label for="available"></label> </span>
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

    <button type="submit" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2">
        <i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.doctor')}}</button>
    <a href="{!! route('doctors.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
