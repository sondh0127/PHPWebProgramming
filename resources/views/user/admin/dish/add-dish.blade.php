@extends('layouts.app')

@section('title')
Add Dish
@endsection

@section('content')
{{--Page header--}}
<div class="row">
  <div class="col-sm-12">
    <div class="btn-group pull-right m-t-15">
      <a href="{{url('/all-dish')}}" class="btn btn-default waves-effect">All Dish <span class="m-l-5"></span></a>
    </div>

    <h4 class="page-title">Create New Dish </h4>
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
    <a href="{{url('/add-dish')}}" data-toggle="tab" aria-expanded="true">
      <span class="visible-xs"><i class="fa fa-cutlery"></i></span>
      <span class="hidden-xs">Dish Name</span>
    </a>
  </li>
  <li class="disabled">
    <a href="javascript:void(0);" data-toggle="tab" aria-expanded="false">
      <span class="visible-xs"><i class="fa fa-usd"></i></span>
      <span class="hidden-xs">Dish Price</span>
    </a>
  </li>
  <li class="disabled">
    <a href="javascript:void(0);" data-toggle="tab" aria-expanded="false">
      <span class="visible-xs"><i class="fa fa-photo"></i></span>
      <span class="hidden-xs">Dish Images</span>
    </a>
  </li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="home">
    <form class="form-horizontal" role="form" action="{{url('/save-dish')}}" id="addEmployee" method="post"
      enctype="multipart/form-data" data-parsley-validate novalidate>
      {{csrf_field()}}

      <div class="form-group">
        <label for="" class="col-md-2 control-label">Thumbnail <span class="text-danger">*</span> </label>
        <div class="col md-10">
          <div id="image-preview">
            <label for="image-upload" id="image-label">Choose Photo</label>
            <input type="file" name="thumbnail" id="image-upload" required />
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-2 control-label">Dish Name <span class="text-danger">*</span></label>
        <div class="col-md-8">
          <input type="text" name="dish" class="form-control" value="" placeholder="Dish Name" parsley-trigger="change"
            maxlength="50" required>
        </div>
      </div>


      <div class="form-group">
        <label class="col-md-2 control-label"></label>
        <div class="col-md-10">
          <button type="submit" class="ladda-button btn btn-purple" data-style="expand-right">
            Save Dish And Go Next
          </button>
        </div>
      </div>

    </form>
  </div>
</div>






@endsection

@section('extra-js')



@endsection
