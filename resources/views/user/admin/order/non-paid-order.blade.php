@extends('layouts.app')

@section('title')
    All Order
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-t-15">
                <a href="{{url('/new-order')}}" class="btn btn-default waves-effect">New Order <span
                            class="m-l-5"></span></a>
            </div>

            <h4 class="page-title">Non-paid Order</h4>
            <ol class="breadcrumb">
                <li>
                    <a href="{{url('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{url('/all-order')}}">Order</a>
                </li>
                <li class="active">
                    Non-paid Order
                </li>
            </ol>
        </div>
    </div>
    <div class="card-box table-responsive">
        <table id="datatable-responsive"
               class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Order No</th>
                <th>Table No</th>
                <th>Served By</th>
                <th>Order Value</th>
                <th>Kitchen</th>
                <th>Status</th>
                <th width="120px">Action</th>
            </tr>
            </thead>
            <?php $count = 1; ?>
            <tbody>
            @foreach($orders as $order)
                <?php
                $orderSum = $order->orderPrice->sum('gross_price');
                ?>
                <tr>
                    <td>{{str_pad($order->id,4,0,STR_PAD_LEFT)}}</td>
                    <td>{{$order->table ? $order->table->table_no : "Table not selected"}}</td>
                    <td>{{$order->servedBy->name}}</td>
                    <td>
                        {{config('restaurant.currency.symbol')}} {{number_format($orderSum,2)}} {{config('restaurant.currency.currency')}}
                        {{--<dl class="dl-horizontal m-b-0">--}}
                        {{--<dt>--}}
                        {{--Order Value :--}}
                        {{--</dt>--}}
                        {{--<dd>--}}
                        {{--{{config('restaurant.currency.symbol')}} {{number_format($orderSum,'00','.',',')}} {{config('restaurant.currency.currency')}}--}}
                        {{--</dd>--}}
                        {{--<dt>--}}
                        {{--Order Value inc vat:--}}
                        {{--</dt>--}}
                        {{--<dd>--}}
                        {{--{{config('restaurant.currency.symbol')}} {{number_format($orderSum+($orderSum*$order->vat)/100,'00','.',',')}} {{config('restaurant.currency.currency')}}--}}
                        {{--</dd>--}}
                        {{--<dt>--}}
                        {{--Vat:--}}
                        {{--</dt>--}}
                        {{--<dd>--}}
                        {{--{{$order->vat}} %--}}
                        {{--</dd>--}}
                        {{--<dt>--}}
                        {{--Cache:--}}
                        {{--</dt>--}}
                        {{--<dd>--}}
                        {{--{{$order->payment}}--}}
                        {{--</dd>--}}
                        {{--<dt>--}}
                        {{--Change:--}}
                        {{--</dt>--}}
                        {{--<dd>--}}
                        {{--{{$order->change_amount}}--}}
                        {{--</dd>--}}
                        {{--</dl>--}}
                    </td>
                    <td>{{$order->kitchen ? $order->kitchen->name : "No-Kitchen"}}</td>
                    <td>
                        @if($order->status == 0)
                            <span class="text-warning">Pending.....</span>
                        @elseif($order->status == 1)
                            <i>Cooking.....</i>
                        @elseif($order->status == 2)
                            <b class="text-custom"><i>Cooked !</i></b>
                        @elseif($order->status == 3)
                            <b class="text-danger"> Served !</b>
                        @else
                            Unknown Status
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{url('/edit-order/'.$order->id)}}"
                               class="btn btn-success waves-effect waves-light">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="{{url('/print-order/'.$order->id)}}"
                               class="btn btn-purple waves-effect waves-light">
                                <i class="fa fa-print"></i>
                            </a>
                            @if($order->status == 0)
                                <a href="#" onclick="$(this).confirmDelete('/delete-order/{{$order->id}}')"
                                   class="btn btn-danger waves-effect waves-light">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function () {
            $("#datatable-responsive").DataTable({
                order: [0, 'desc']
            });
        })
    </script>

@endsection