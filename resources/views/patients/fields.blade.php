@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-6">
    <!-- Image Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('image', trans("lang.patient_image"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
            </div>
            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
            <div class="form-text text-muted w-50">
                {{ trans("lang.patient_image_help") }}
            </div>
        </div>
    </div>
    @prepend('scripts')
        <script type="text/javascript">
            var var1666017496295880374ble = [];
            @if(isset($patient) && $patient->hasMedia('image'))
            @forEach($patient->getMedia('image') as $media)
            var1666017496295880374ble.push({
                name: "{!! $media->name !!}",
                size: "{!! $media->size !!}",
                type: "{!! $media->mime_type !!}",
                uuid: "{!! $media->getCustomProperty('uuid'); !!}",
                thumb: "{!! $media->getUrl('thumb'); !!}",
                collection_name: "{!! $media->collection_name !!}"
            });
            @endforeach
            @endif
            var dz_var1666017496295880374ble = $(".dropzone.image").dropzone({
                url: "{!!url('uploads/store')!!}",
                addRemoveLinks: true,
                maxFiles: 5 - var1666017496295880374ble.length,
                init: function () {
                    @if(isset($patient) && $patient->hasMedia('image'))
                    var1666017496295880374ble.forEach(media => {
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
                    dz_var1666017496295880374ble[0].mockFile = file;
                },
                removedfile: function (file) {
                    dzRemoveFileMultiple(
                        file, var1666017496295880374ble, '{!! url("patients/remove-media") !!}',
                        'image', '{!! isset($patient) ? $patient->id : 0 !!}', '{!! url("uploads/clear") !!}', '{!! csrf_token() !!}'
                    );
                }
            });
            dz_var1666017496295880374ble[0].mockFile = var1666017496295880374ble;
            dropzoneFields['image'] = dz_var1666017496295880374ble;
        </script>
    @endprepend


<!-- User Id Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('user_id', trans("lang.patient_user_id"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::select('user_id', $user, null, ['class' => 'select2 form-control']) !!}
    <div class="form-text text-muted">{{ trans("lang.patient_user_id_help") }}</div>
  </div>
</div>


<!-- First Name Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('first_name', trans("lang.patient_first_name"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('first_name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_first_name_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_first_name_help") }}
    </div>
  </div>
</div>


<!-- Last Name Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('last_name', trans("lang.patient_last_name"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('last_name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_last_name_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_last_name_help") }}
    </div>
  </div>
</div>


{{--<!-- ID Card Field -->--}}
{{--    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">--}}
{{--        {!! Form::label('id_card', trans("lang.patient_id_card"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}--}}
{{--        <div class="col-md-9">--}}
{{--            <div style="width: 100%" class="dropzone id_card" id="id_card" data-field="id_card">--}}
{{--            </div>--}}
{{--            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>--}}
{{--            <div class="form-text text-muted w-50">--}}
{{--                {{ trans("lang.patient_id_card_help") }}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    @prepend('scripts')--}}
{{--        <script type="text/javascript">--}}
{{--            var var1666017496669196819ble = [];--}}
{{--            @if(isset($patient) && $patient->hasMedia('card_id'))--}}
{{--            @forEach($patient->getMedia('card_id') as $card_id)--}}
{{--            var1666017496669196819ble.push({--}}
{{--                name: "{!! $card_id->name !!}",--}}
{{--                size: "{!! $card_id->size !!}",--}}
{{--                type: "{!! $card_id->mime_type !!}",--}}
{{--                uuid: "{!! $card_id->getCustomProperty('uuid'); !!}",--}}
{{--                thumb: "{!! $card_id->getUrl('thumb'); !!}",--}}
{{--                collection_name: "{!! $card_id->collection_name !!}"--}}
{{--            });--}}
{{--            @endforeach--}}
{{--            @endif--}}
{{--            var dz_var1666017496669196819ble = $(".dropzone.image").dropzone({--}}
{{--                url: "{!!url('uploads/store')!!}",--}}
{{--                addRemoveLinks: true,--}}
{{--                maxFiles: 5 - var1666017496669196819ble.length,--}}
{{--                init: function () {--}}
{{--                    @if(isset($patient) && $patient->hasMedia('card_id'))--}}
{{--                    var1666017496669196819ble.forEach(card_id => {--}}
{{--                        dzInit(this, card_id, card_id.thumb);--}}
{{--                    });--}}
{{--                    @endif--}}
{{--                },--}}
{{--                accept: function (file, done) {--}}
{{--                    dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");--}}
{{--                },--}}
{{--                sending: function (file, xhr, formData) {--}}
{{--                    dzSendingMultiple(this, file, formData, '{!! csrf_token() !!}');--}}
{{--                },--}}
{{--                complete: function (file) {--}}
{{--                    dzCompleteMultiple(this, file);--}}
{{--                    dz_var1666017496669196819ble[0].mockFile = file;--}}
{{--                },--}}
{{--                removedfile: function (file) {--}}
{{--                    dzRemoveFileMultiple(--}}
{{--                        file, var1666017496669196819ble, '{!! url("patients/remove-media") !!}',--}}
{{--                        'card_id', '{!! isset($patient) ? $patient->id : 0 !!}', '{!! url("uploads/clear") !!}', '{!! csrf_token() !!}'--}}
{{--                    );--}}
{{--                }--}}
{{--            });--}}
{{--            dz_var1666017496669196819ble[0].mockFile = var1666017496669196819ble;--}}
{{--            dropzoneFields['card_id'] = dz_var1666017496669196819ble;--}}
{{--        </script>--}}
{{--    @endprepend--}}


<!-- Phone Number Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('phone_number', trans("lang.patient_phone_number"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('phone_number', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_phone_number_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_phone_number_help") }}
    </div>
  </div>
</div>

</div>
<div class="d-flex flex-column col-sm-12 col-md-6">

<!-- Mobile Number Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('mobile_number', trans("lang.patient_mobile_number"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('mobile_number', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_mobile_number_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_mobile_number_help") }}
    </div>
  </div>
</div>


<!-- Age Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('age', trans("lang.patient_age"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('age', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_age_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_age_help") }}
    </div>
  </div>
</div>


<!-- Gender Field -->
{{--<div class="form-group align-items-baseline d-flex flex-column flex-md-row">--}}
{{--  {!! Form::label('gender', trans("lang.patient_gender"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}--}}
{{--  <div class="col-md-9">--}}
{{--    {!! Form::text('gender', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_gender_placeholder")]) !!}--}}
{{--    <div class="form-text text-muted">--}}
{{--      {{ trans("lang.patient_gender_help") }}--}}
{{--    </div>--}}
{{--  </div>--}}
{{--</div>--}}

<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('gender', trans("lang.patient_gender"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        {!! Form::select('gender', ['male' => trans('lang.patient_male'),'fixed' => trans('lang.patient_female')], null, ['class' => 'select2 form-control']) !!}
        <div class="form-text text-muted">{{ trans("lang.patient_gender_help") }}</div>
    </div>
</div>
<!-- Weight Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('weight', trans("lang.patient_weight"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('weight', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_weight_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_weight_help") }}
    </div>
  </div>
</div>


<!-- Height Field -->
<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
  {!! Form::label('height', trans("lang.patient_height"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
  <div class="col-md-9">
    {!! Form::text('height', null,  ['class' => 'form-control','placeholder'=>  trans("lang.patient_height_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.patient_height_help") }}
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
  <button type="submit" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2" ><i class="fas fa-save"></i> {{trans('lang.save')}} {{trans('lang.patient')}}</button>
  <a href="{!! route('patients.index') !!}" class="btn btn-default"><i class="fas fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
