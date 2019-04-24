@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('content')
@admin
@include('user.admin.dashboard')
@endadmin

@kitchen
@include('user.kitchen.dashboard')
@endkitchen

@waiter
@include('user.waiter.dashboard')
@endwaiter

@manager
@include('user.admin.dashboard')
@endmanager

@endsection
