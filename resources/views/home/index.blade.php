@extends('layouts.homeapp')


@section('content')
<div class="homeBody">
    <div class="searcharea">
        <div class="row justifycontentspacearound">
            <div>
                <h1>Welcome to Comiere</h1>
            </div>
        </div>
        <br />
        <div class="row justifycontentspacearound">
            <h3>Find, compare and book in-network doctors</h3>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8 form-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="search_text"
                        placeholder="Search doctors with name, speciality, address...">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="working_content">
        <div class="row search_filter_result">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <img class="card-img-top" src="{{asset('images/default.png')}}" alt="Card image">
                                </div>
                                <div class="col-md-9">
                                    <div><h3>doctor name</h3></div>
                                    <div><p>doctor speciality</p></div>
                                    <div><p>doctor address</p></div>
                                    <div><p>doctor review</p></div>
                                    <div><p>doctor experience</p></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row promised_area">

        </div>
    </div>
</div>
@endsection


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript">

    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".filterselect").click(function () {
            $.ajax({
                url: '/searching/' + search_value,
                method: 'GET'
            }).done(function (response) {
                $('#results').html(response);
            });
        });

    });

</script>


<style>
    .homeBody {
        padding-top: 20px;
    }

    .searcharea {
        background-color: #e2f0ff;
        margin-left: 200px;
        margin-right: 200px;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    h1 {
        font-weight: 900 !important;
    }

    .justifycontentspacearound {
        justify-content: space-around;
    }

    .working_content {
        padding: 20px;
        padding-left: 200px;
        padding-right: 200px;
    }

    .card {
        width: 100%;
    }

    .card-img-top {
        width: 100%;
        height: auto;
    }
</style>