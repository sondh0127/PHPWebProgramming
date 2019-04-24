@extends('layouts.app')

@section('content')
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
  <div class=" card-box">
    <div class="panel-heading">
      <h3 class="text-center"> Admin Registration </h3>
    </div>


    <div class="panel-body">
      <form class="form-horizontal m-t-20" action="{{ route('register') }}" method="post" data-parsley-validate
        novalidate>
        {{csrf_field()}}
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required placeholder="Full Name" name="name"
              value="{{ old('name') }}">
            @if ($errors->has('name'))
            <span class="help-block">
              <strong class="text-danger">{{ $errors->first('name') }}</strong>
            </span>
            @endif
          </div>
        </div>
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="email" required placeholder="Email Address" name="email"
              value="{{ old('email') }}">
            @if ($errors->has('email'))
            <span class="help-block">
              <strong class="text-danger">{{ $errors->first('email') }}</strong>
            </span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required name="password" placeholder="Password">
            @if ($errors->has('password'))
            <span class="help-block">
              <strong class="text-danger">{{ $errors->first('password') }}</strong>
            </span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required name="password_confirmation"
              placeholder="Confirm Password">
            @if ($errors->has('password_confirmation'))
            <span class="help-block">
              <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
            </span>
            @endif
          </div>
        </div>



        <div class="form-group text-center m-t-40">
          <div class="col-xs-12">
            <button class="btn btn-pink btn-block text-uppercase waves-effect waves-light" type="submit">Sign
              Up</button>
          </div>
        </div>

      </form>

    </div>
  </div>
  <div class="row">
    <div class="col-sm-12 text-center">
      <p>Already have an account? <a href="{{ route('login') }}" class="text-primary m-l-5"><b>Login</b></a></p>
    </div>
  </div>

</div>
@endsection
