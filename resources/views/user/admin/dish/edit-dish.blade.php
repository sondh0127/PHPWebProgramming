@extends('layouts.app')

@section('title')
Dish Price - {{$dish->dish}}
@endsection

@section('content')
{{--Page header--}}
<div class="row">
  <div class="col-sm-12">
    <div class="btn-group pull-right m-t-15">
      <a href="{{url('/all-dish')}}" class="btn btn-default waves-effect">All Dish <span class="m-l-5"></span></a>
    </div>

    <h4 class="page-title">Edit Dish </h4>
    <ol class="breadcrumb">
      <li>
        <a href="{{url('/')}}">Home</a>
      </li>
      <li class="active">
        Dish
      </li>
      <li class="active">
        Edit Dish
      </li>
    </ol>
  </div>
</div>
<ul class="nav nav-tabs">
  <li class="active">
    <a href="{{url('/edit-dish/'.$dish->id)}}" aria-expanded="true">
      <span class="visible-xs"><i class="fa fa-cutlery"></i></span>
      <span class="hidden-xs">{{$dish->dish}}</span>
    </a>
  </li>
  <li class="">
    <a href="{{url('/dish-price/'.$dish->id)}}" aria-expanded="false">
      <span class="visible-xs"><i class="fa fa-usd"></i></span>
      <span class="hidden-xs">Dish Price</span>
    </a>
  </li>
  <li class="">
    <a href="{{url('/dish-image/'.$dish->id)}}" aria-expanded="false">
      <span class="visible-xs"><i class="fa fa-photo"></i></span>
      <span class="hidden-xs">Dish Images</span>
    </a>
  </li>
  <li class="">
    <a href="{{url('/dish-recipe/'.$dish->id)}}" aria-expanded="false">
      <span class="visible-xs"><i class="fa fa-photo"></i></span>
      <span class="hidden-xs">Recipe</span>
    </a>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="home">
    <form class="form-horizontal" role="form" action="#" id="updateDish" method="post" enctype="multipart/form-data"
      data-parsley-validate novalidate>
      {{csrf_field()}}
      <input type="hidden" value="{{$dish->id}}" id="dishId">
      <div class="form-group">
        <label for="" class="col-md-2 control-label">Thumbnail <span class="text-danger">*</span> </label>
        <div class="col md-10">
          <div id="image-preview" style="background-image: url({{$dish->thumbnail}})">
            <label for="image-upload" id="image-label">Choose Photo</label>
            <input type="file" name="thumbnail" id="image-upload" />
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-2 control-label">Dish Name <span class="text-danger">*</span></label>
        <div class="col-md-8">
          <input type="text" name="dish" class="form-control" value="{{$dish->dish}}" placeholder="Dish Name"
            parsley-trigger="change" maxlength="50" required>
        </div>
      </div>



      <div class="checkbox checkbox-custom checkbox-circle col-md-offset-2">
        <input id="checkbox71" name="status" type="checkbox" {{$dish->status == 1 ? 'checked' : ''}}>
        <label for="checkbox71">
          Available
        </label>
      </div>

      <br>
      <div class="form-group">
        <label class="col-md-2 control-label"></label>
        <div class="col-md-10">
          <button type="submit" class="btn btn-purple">Update Dish</button>
        </div>
      </div>

    </form>
  </div>
</div>
@endsection

@section('extra-js')

<script>
$(document).ready(function() {
  $("#updateDish").on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var id = $("#dishId").val();
    $(this).speedPost('/update-dish/' + id, formData, message = {
      success: {
        header: 'Dish Update successfully',
        body: 'Dish updated successfully'
      },
      error: {
        header: 'Dish  already exist',
        body: 'Dish  found'
      },
      warning: {
        header: 'Internal Server Error',
        body: 'Internal server error'
      }
    });
  })
});
</script>

@endsection
