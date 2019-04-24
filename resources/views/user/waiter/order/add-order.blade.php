@extends('layouts.app')

@section('title')
New Order
@endsection

@section('content')

<div class="card-box">
  <form class="form-horizontal" id="orderForm" method="post" role="form" data-parsley-validate novalidate>

    {{csrf_field()}}

    <input type="hidden" value="{{config('restaurant.vat.vat_percentage')}}" id="vat_percentage">

    <div class="form-group">
      <label for="inputEmail3" class="col-sm-2 control-label">Select Table</label>
      <div class="col-sm-2">
        <select name="table" id="tale" class="form-control">
          <option value="0">Select Table</option>
          @foreach($tables as $table)
          <option value="{{$table->id}}">{{$table->table_no}}</option>
          @endforeach
        </select>
      </div>
      <label for="hori-pass1" class="col-sm-2 control-label">Select Dish*</label>
      <div class="col-sm-4">
        <select name="dish" id="dish" required class="form-control">
          <option value="">Select One</option>
          @foreach($dishes as $dish)
          <option value="{{$dish->id}}">{{$dish->dish}}</option>
          @endforeach
        </select>
      </div>

    </div>


    <div class="form-group">
      <label for="hori-pass2" class="col-sm-2 control-label">Dish Type *</label>
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
      <div class="col-sm-3">
        <input type="number" min="1" disabled required class="form-control" id="price" placeholder="Quantity">
      </div>
    </div>

    <div class="form-group">

    </div>

    <div class="form-group">
      <div class="col-sm-offset-4 col-sm-8">
        <button type="submit" class="btn btn-primary waves-effect waves-light">
          Add Dish
        </button>
        <button type="reset" class="btn btn-default waves-effect waves-light m-l-5">
          Clear
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
        <th style="border: 1px solid black">Quantity</th>
        <th style="border: 1px solid black">Price ({{config('restaurant.currency.currency')}})</th>
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
var dishInfo;
var dishList = [];

var dish_info = [];

var dish_id = 0;
var dish_type_id;
var dish_price = 0;

var total_price = 0;

var payment = 0;
var vat = 0;
var change = 0;

$(document).ready(function() {

  vat = $("#vat_percentage").val();

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
        // console.log(dish_info[i]);
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

  $("#submitOrder").on('click', function() {
    // console.log('Clicked');
    var saveOrder = {
      _token: $("input[name=_token]").val(),
      table_id: $("#tale").val(),
      dishList: dishList
    }
    $.post("/save-order", saveOrder, function(data) {
      //    console.log(data);
    });
  });

  $("#orderForm").on('submit', function(e) {
    e.preventDefault();
    total_price = parseInt($("#price").val()) + parseInt(total_price);
    // console.log(total_price);
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

  //Open Model
  $.fn.deleteDishFromList = function(index) {
    // console.log(dishList[index]);
    var con = confirm("Are you sure ?");
    if (con) {
      console.log('Confirm' + index);
      dishList.splice(index, 1);
      $("#orderDetails").empty();
      $("#orderSummary").empty();
      $(this).renderDishList(dishList);
    }
  };

  $.fn.saveOrder = function() {
    // console.log('Order Submit');
    var saveOrder = {
      _token: $("input[name=_token]").val(),
      table_id: $("#tale").val(),
      payment: payment,
      dishList: dishList,
      vat: vat,
      change: change
    };
    $.post("/save-order", saveOrder, function(data) {
      // console.log(data);
    }).done(function() {
      $.Notification.notify('success',
        'bottom right',
        'Success ! Order Has been done successfully');
      dishList = [];
      $("#orderDetails").empty();
      $("#orderSummary").empty();
      $(this).renderDishList(dishList);
    });
  };

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
            text: "Price inc Vat"
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
              value: "",
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
              value: "",
              id: "change_text_field",
              disabled: "disabled"
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
              class: "btn btn-success ladda-button",
              text: "Submit Order",
              onClick: "$(this).saveOrder()"
            })
          )
        )
      )
    };

    $.fn.showChange = function(data) {
      // console.log('Change' + $(this).val());
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

  }
});
</script>

@endsection
