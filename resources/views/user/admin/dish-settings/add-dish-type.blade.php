@extends('layouts.app')

@section('title')
    Add Dish Type
@endsection

@section('content')
    <div class="card-box">
        <h4 class="m-t-0 header-title"><b>New Dish Type</b></h4>
        <p class="text-muted font-13 m-b-30">
            Description for new dish
        </p>
        <form class="form-horizontal" role="form" id="unitForm" method="POST" data-parsley-validate novalidate>
            {{csrf_field()}}
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Dish*</label>
                <div class="col-sm-7">
                    <input type="text" required  class="form-control" name="dish" placeholder="1/2, 3/4">
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-8">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        Save Unit
                    </button>

                </div>
            </div>
        </form>

    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function () {
            var unitForm = $("#unitForm");

            unitForm.on('submit',function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $(this).speedPost('/save-dish-type', formData, message = {
                    success: {header: 'Unit saved successfully', body: 'Unit updated successfully'},
                    error: {header: 'Unit address already exist', body: 'Unit address found'},
                    warning: {header: 'Internal Server Error', body: 'Internal server error'}
                },unitForm);
            });
        });
    </script>
@endsection