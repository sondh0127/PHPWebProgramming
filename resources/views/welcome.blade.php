<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>My Restaurant Home</title>
  @include('assets.css')
  <style>
  .badge {
    font-size: 20px !important;
  }

  .dish {
    padding-bottom: 25px;
    border: 1px solid orangered;
    margin-bottom: 25px;
    /*margin-right: 1px;*/
  }
  </style>
</head>

<body>
  <!-- Top Bar Start -->
  <div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
      <div class="text-center">
        <a href="{{ url('/') }}" class="logo">
          <i class="icon-c-logo my-logo">M</i>
          <span class="my-logo">My Restaurant</span>
        </a>
      </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
      <div class="container">
        <div class="">
          @if (Route::has('login'))
          <ul class="nav navbar-nav hidden-xs pull-right">
            @if (Auth::check())
            <li>
              <a href="{{ url('/home') }}" class="waves-effect waves-light">Home</a>
            </li>
            @else
            <li>
              <a href="{{ route('login') }}" class="waves-effect waves-light">Login</a>
            </li>
            <!-- <li><a href="{{route('register')}}" class="waves-effect waves-light">Join</a></li> -->
            @endif
          </ul>
          @endif
        </div>
        <!--/.nav-collapse -->
      </div>
    </div>
  </div>
  <!-- Top Bar End -->

  <?php $dishes = \App\Model\Dish::where('status', 1)->get(); ?>

  <div style="margin-top: 70px">
    <div class="card-box">
      <center>
        <h1>Menus</h1>
      </center>
      <div class="dishes">
        <div class="row">
          @foreach($dishes as $dish)
          <div class="col-md-6 dish">
            <div class="col-md-6">
              <img src="{{\Illuminate\Support\Facades\Storage::disk('s3')->url($dish->thumbnail)}}" alt=""
                class="img-thumbnail" />
            </div>
            <div class="col-md-6">
              <h3>
                <u>{{$dish->dish}}</u>
              </h3>
              <ul class="list-group">
                @foreach($dish->dishPrices as $price)
                <li class="list-group-item">
                  <span class="badge">{{ config('restaurant.currency.symbol')
                      }}{{number_format($price->price,2)}}</span>
                  {{$price->dish_type}}
                </li>
                @endforeach
              </ul>
            </div>
            <div class="col-md-12">
              @foreach($dish->dishImages as $image)
              <div class="col-md-3">
                <img src="{{\Illuminate\Support\Facades\Storage::disk('s3')->url($image->image)}}"
                  class="img-responsive img-thumbnail" alt="" />
              </div>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</body>

</html>
