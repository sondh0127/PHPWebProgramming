@extends('layouts.app')

@section('title')
New Order
@endsection

@section('content')

<div class="card-box">
  <input type="hidden" id="_payment" value="{{$order->payment}}">
  <input type="hidden" id="_change" value="{{$order->change_amount}}">
  <input type="hidden" id="_vat" value="{{$order->vat}}">

  <form class="form-horizontal" id="orderForm" method="post" role="form" data-parsley-validate novalidate>

    {{csrf_field()}}
    <input type="hidden" value="{{config('restaurant.vat.vat_percentage')}}" id="vat_percentage">
    <input type="hidden" value="{{$order->id}}" id="order_id">
    <div class="form-group">
      <input type="hidden" value="{{$order->table_id}}">
      <label for="hori-pass1" class="col-sm-2 control-label">Select Dish*</label>
      <div class="col-sm-2">
        <select name="dish" id="dish" required class="form-control">
          <option value="">Select One</option>
          @foreach($dishes as $dish)
          <option value="{{$dish->id}}">{{$dish->dish}}</option>
          @endforeach
        </select>
      </div>

      <label for="hori-pass2" class="col-sm-1 control-label">Dish Type *</label>
      <div class="col-sm-2">
        <select name="dish_type" id="dishType" required class="form-control">
          <option value="">Select One</option>
        </select>
      </div>

      <label for="webSite" class="col-sm-1 control-label">Quantity</label>
      <div class="col-sm-1">
        <input type="number" min="1" value="1" required class="form-control" id="quantity" placeholder="Quantity">
      </div>

      <label for="webSite" class="col-sm-1 control-label">Price</label>
      <div class="col-sm-2">
        <input type="number" min="1" disabled required class="form-control" id="price" placeholder="Quantity">
      </div>

    </div>


    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-8">
        <button type="submit" class="btn btn-primary waves-effect waves-light">
          Add Dish
        </button>
        <button type="reset" class="btn btn-default waves-effect waves-light m-l-5">
          Cancel
        </button>
      </div>
    </div>
  </form>
</div>

<div class="card-box">
  <table class="table table-responsive">
    <thead>
      <tr>
        <th style="border: 1px solid black">Dish Name</th>
        <th width="10%" style="border: 1px solid black">Type</th>
        <th style="border: 1px solid black">Q</th>
        <th style="border: 1px solid black">Price</th>
        <th width="5%" style="border: 1px solid black">Action</th>
      </tr>
    </thead>
    <tbody id="orderDetails">

    </tbody>
  </table>
  <div class="row">
    <div class="col-sm-6">
      <div class="form-horizontal" id="orderSummary">

      </div>
    </div>
  </div>
</div>

@endsection

@section('extra-js')

<script>
var dishList = [];
var total_price = 0;
var payment = $("#_payment").val();
var vat = $("#_vat").val();
var change = $("#_change").val();
$(document).ready(function() {
  var id = $("#order_id").val();
  vat = $("#vat_percentage").val();
  console.log('Edit Order Ready.....................' + id);
  $.get('/get-order-details/' + id, function(data) {
    $.each(data, function(index, dish) {
      dishInfo = {
        dish_id: dish.dish_id,
        dish_name: dish.dish.dish,
        dish_type_id: dish.dish_type_id,
        dish_type_name: dish.dish_type.dish_type,
        quantity: dish.quantity,
        price: dish.net_price
      };
      dishList.push(dishInfo);
    });

    $("#orderDetails").empty();
    $("#orderSummary").empty();
    $(this).renderDishList(dishList);

  });

  $("#orderForm").on('submit', function(e) {
    e.preventDefault();
    total_price = parseInt($("#price").val()) + parseInt(total_price);
    console.log(total_price);
    $("#totalPrice").text(total_price);
    $("#totalDish").text(dishList.length + 1);
    dishInfo = {
      dish_id: dish_id,
      dish_name: $("#dish option:selected").text(),
      dish_type_id: dish_type_id,
      dish_type_name: $("#dishType option:selected").text(),
      quantity: $("#quantity").val(),
      price: $("#price").val()
    }

    dishList.push(dishInfo);
    $("#orderDetails").empty();
    $("#orderSummary").empty();
    $(this).renderDishList(dishList);
    $(this).trigger('reset');


  });

  $.fn.renderDishList = function(data) {
    var total = 0;
    $.each(data, function(index, dish) {
      total += parseInt(dish.price * dish.quantity);
      $("#orderDetails").append(
        $("<tr>").append(
          $("<td>", {
            text: dish.dish_name
          }),
          $("<td>", {
            text: dish.dish_type_name
          }),
          $("<td>", {
            text: dish.quantity
          }),
          $("<td>", {
            text: dish.price * dish.quantity
          }),
          $("<td>").append(
            $("<button>", {
              class: "btn btn-danger btn-xs",
              onClick: "$(this).deleteDishFromList(" + index + ")"
            }).append(
              $("<span>", {
                class: "fa fa-trash"
              })
            )
          )
        )
      )
    });
    if (dishList.length != 0) {

      $("#orderDetails").append(
        $("<tr>").append(
          $("<td>", {
            colspan: 3,
            text: "Total :",
            class: "text-right"
          }),
          $("<th>", {
            text: total
          })
        )
      )

      $("#orderSummary").append(
        $("<div>", {
          class: "form-group"
        }).append(
          $("<label>", {
            class: "col-sm-2 control-label",
            text: "Total"
          }),
          $("<div>", {
            class: "col-sm-5"
          }).append(
            $("<input/>", {
              class: "form-control",
              type: "number",
              value: Math.round(total + (total * vat) / 100),
              disabled: "disabled"
            })
          )
        )
      )
      $("#orderSummary").append(
        $("<div>", {
          class: "form-group"
        }).append(
          $("<label>", {
            class: "col-sm-2 control-label",
            text: "Payment"
          }),
          $("<div>", {
            class: "col-sm-5"
          }).append(
            $("<input />", {
              class: "form-control",
              type: "number",
              value: $("#_payment").val(),
              min: 1,
              onChange: "$(this).showChange(" + total + ")",
              onKeyup: "$(this).showChange(" + total + ")"
            })
          )
        )
      )
      $("#orderSummary").append(
        $("<div>", {
          class: "form-group"
        }).append(
          $("<label>", {
            class: "col-sm-2 control-label",
            text: "Change"
          }),
          $("<div>", {
            class: "col-sm-5"
          }).append(
            $("<input/>", {
              class: "form-control",
              type: "number",
              value: change,
              disabled: "disabled",
              id: "change_text_field"
            })
          )
        )
      )
      $("#orderSummary").append(
        $("<div>", {
          class: "form-group"
        }).append(
          $("<div>", {
            class: "col-sm-5"
          }).append(
            $("<button>", {
              class: "btn btn-success",
              text: "Submit Update Order",
              onClick: "$(this).saveOrder()"
            })
          )
        )
      )
    }
  };

  $.fn.showChange = function(data) {
    console.log('Change' + $(this).val());
    payment = $(this).val();
    if ($(this).val() != "") {
      if (data - $(this).val() < 0) {
        change = Math.round(data + (data * vat) / 100) - $(this).val();
        $("#change_text_field").val(Math.round(data + (data * vat) / 100) - $(this).val());
      }

    } else {
      change = 0;
      $("#change_text_field").val(0);
    }

  };

  // Submit Order Update
  $.fn.saveOrder = function() {
    console.log("Clicked");
    var saveOrder = {
      _token: $("input[name=_token]").val(),
      table_id: $("#tale").val(),
      payment: payment,
      change: $("#change_text_field").val(),
      vat: vat,
      dishList: dishList
    }
    $.post("/update-order/" + id, saveOrder, function(data) {
      $.Notification.notify('warning', 'top right', 'Please wait..............................');
    }).done(function(data) {
      dishList = [];
      $.Notification.notify('success',
        'bottom right',
        'Success ! Order Has been updated successfully', 'Order Has been updated successfully');
      $("#orderDetails").empty();
      $("#orderSummary").empty();
      $(this).renderDishList(dishList);
    });
  };

  //Open Model
  $.fn.deleteDishFromList = function(index) {
    console.log(dishList[index]);
    var con = confirm("Are you sure ?");
    if (con) {
      console.log('Confirm' + index);
      dishList.splice(index, 1);
      $("#orderDetails").empty();
      $("#orderSummary").empty();
      $(this).renderDishList(dishList);
    }
  };


  // Dish Dropdown on change event
  $("#dish").on('change', function() {
    dish_id = $(this).val();
    dish_type_id = '';
    $.get('/dish-types/' + dish_id, function(data) {
      dish_info = data;
      $("#dishType").empty();
      $("#dishType").append(
        $("<option>", {
          text: "Select Dish Type"
        })
      )
      $.each(data, function(index, dish_type) {
        $("#dishType").append(
          $("<option>", {
            text: dish_type.dish_type,
            val: dish_type.id
          })
        )
      });

    })
  });

  // Dish Type Dropdown on change event
  $("#dishType").on('change', function() {
    dish_type_id = $(this).val();
    for (var i = 0; i <= dish_info.length; i++) {
      if (dish_info[i].id == $(this).val()) {
        var dish_details = dish_info[i];
        console.log(dish_info[i]);
        $("#price").val($("#quantity").val() * dish_details.price);
        dish_price = $("#quantity").val() * dish_details.price;
        break;
      }
    }
  });

  // Quantity field on keyup event
  $("#quantity").on('keyup', function() {
    $("#price").val(dish_price * $(this).val());
  });

  $("#quantity").on('change', function() {
    $("#price").val(dish_price * $(this).val());
  });

  // Quantity Field on mouse wheen event
  $("#quantity").on('mousewheel', function() {
    $("#price").val(dish_price * $(this).val());
  });

});
</script>

@endsection
