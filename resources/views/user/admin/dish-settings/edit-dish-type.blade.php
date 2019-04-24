@extends('layouts.app')

@section('title')
    Add Dish Type
@endsection

@section('content')
    <div class="card-box">
        <h4 class="m-t-0 header-title"><b>New Dish Type</b></h4>
        <p class="text-muted font-13 m-b-30">
            Description for new dish type
        </p>
        <form class="form-horizontal" role="form" id="unitForm" method="POST" data-parsley-validate novalidate>
            {{csrf_field()}}
            <input type="hidden" value="{{$dish_type->id}}" id="id">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Dish*</label>
                <div class="col-sm-7">
                    <input type="text" required  class="form-control" name="dish" value="{{$dish_type->dish}}" placeholder="1/2, 3/4">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label"></label>
                <div class="col-sm-7">
                    <div class="checkbox checkbox-custom">
                        <input id="checkbox11" name="status" type="checkbox" {{$dish_type->status ==1 ? 'checked' : ''}}>
                        <label for="checkbox11">
                            Active
                        </label>
                    </div>
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
            var id = $("#id").val();
            unitForm.on('submit',function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $(this).speedPost('/update-dish-type/'+id, formData, message = {
                    success: {header: 'Dish type updated successfully', body: 'Dish type updated successfully'},
                    error: {header: 'Dish type already exist', body: 'Dish type found'},
                    warning: {header: 'Internal Server Error', body: 'Internal server error'}
                });
            });
        });
    </script>
@endsection