@extends('layouts.settings.default')
@section('settings_title',trans('lang.user_table'))
@section('settings_content')
    @include('flash::message')
    <div class="card shadow-sm">
        <div class="card-header">
            <ul class="nav nav-tabs d-flex flex-md-row flex-column-reverse align-items-start card-header-tabs">
                <div class="d-flex flex-row">
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.user_table')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{!! route('users.create') !!}"><i class="fas fa-plus mr-2"></i>{{trans('lang.user_create')}}</a>
                    </li>
                    <li class="nav-item">
                        <span class="filter">
                            Filter
                        </span>
                        <select class="filterselect" value='0'>
                            <option value="">All comiere</option>
                            <option value="+92">Pakistan</option>
                            <option value="+44">United Kingdom</option>
                            <option value="+1">United State</option>
                        </select>
                    </li>
                </div>
                @include('layouts.right_toolbar', compact('dataTable'))
            </ul>
        </div>
        <div class="card-body">
            @include('settings.users.table')
            <div class="clearfix"></div>
        </div>
    </div>
    </div>
@endsection

<style>
    .filter{
        color: grey;
        padding-block-start: 5px;
        margin-inline-start: 20px;
    }
    .filterselect{
        color: grey;
        margin-block-start: 5px;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
<script type="text/javascript">


  $(function () {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(".filterselect").click(function(){
        console.log($(this).val())
        $(".form-control._filter").val($(this).val());
        // $("#" + tableId + "_filter input").on('change', function () {
        //     dtable.search(this.value).draw();
        // });
        // dtable.search("+44").draw();
        // dtable.state().search = "+44";
        // dtable.search("+44").draw();
        // console.log($(this).val())
        // $.ajax({
        //     method: "post",
        //     url: "/users/filter",
        //     data: ({
        //         "_token": "{{ csrf_token() }}",
        //         country: $(this).val()
        //     }),
        //     success: function (res) {

        //     },
        //     error: function (res) {
        //     }
        // });
    });

  });
</script>