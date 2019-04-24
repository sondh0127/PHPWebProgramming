@extends('layouts.app')

@section('title')
Dish - {{$dish->dish}}
@endsection

@section('content')
<link rel="stylesheet" href="{{url('/dashboard/plugins/magnific-popup/css/magnific-popup.css')}}">
<div class="card-box">
  <div class="row">
    <div class="col-md-4">
      <img src="{{url($dish->thumbnail)}}" alt="" class="img-responsive">
    </div>
    <div class="col-md-8">
      <h4 class="header-title">Dish Type(s) with price</h4>
      <ul class="list-group">
        @foreach($dish->dishPrices as $dishPrice)
        <li class="list-group-item">
          <span class="pull-right">
            {{config('restaurant.currency.symbol')}}
            <b class="text-success">{{$dishPrice->price}}</b>
            {{config('restaurant.currency.currency')}}
          </span>
          {{$dishPrice->dish_type}}
        </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
<div class="row port">
  <div class="portfolioContainer">
    @foreach($dish->dishImages as $image)
    <?php $url = \Illuminate\Support\Facades\Storage::disk('s3')->url($image->image); ?>
    <div class="col-sm-6 col-lg-3 col-md-4 webdesign illustrator">
      <div class="gal-detail thumb">
        <a href="{{$url}}" class="image-popup" title="{{$image->title}}">
          <img src="{{$url}}" class="thumb-img" alt="work-thumbnail">
        </a>
        <h4>{{$image->title}}</h4>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection

@section('extra-js')
<script type="text/javascript" src="{{url('/dashboard/plugins/isotope/js/isotope.pkgd.min.js')}}"></script>
<script type="text/javascript" src="{{url('/dashboard/plugins/magnific-popup/js/jquery.magnific-popup.min.js')}}">
</script>
<script>
$(document).ready(function() {
  $('.image-popup').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    mainClass: 'mfp-fade',
    gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0, 1]
    }
  });
});
</script>
@endsection
