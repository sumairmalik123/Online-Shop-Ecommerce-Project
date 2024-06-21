@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
        <div class="col-sm-6">
                <h1>Shipping Management</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('categories.list')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <div class="col">
            @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                {{ Session::get('success') }}
            </div>
            @endif
            
            @if (Session::has('error'))
            <div class="alert alert-danger alert-dismissible __web-inspector-hide-shortcut__">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Alert!</h4> {{ Session::get('error') }}
            </div>
            @endif
                  
        </div>
        <form action="" method="post" id="shippingForm" name="shippingForm">
            @csrf
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <select name="country" id="country" class="form-control">
                                    <option value="">Select Country</option>
                                    @if ($countries->isNotEmpty())
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                        <option value="rest_of_world">Rest of the world</option>
                                    @endif
                                </select>
                                <p></p>	    
                            </div>
                        </div>
    						<div class="col-md-4">
                                <div class="mb-3">
                                <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount">
                                <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>				
                    </div>
                </div>	    						
            </div>
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-12">
                           <table class="table table-striped">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            @if ($shippingCharge->isNotEmpty())
                            @foreach ($shippingCharge as $shipping)
                            <tr>
                                <td>{{ $shipping->id }}</td>    
                                <!--<td>{{ $shipping->name }}</td>-->
                                <td>
                                    {{ ($shipping->country_id == 'rest_of_world') ? 'Rest of the world' : $shipping->name }}
                                </td>
                                <td>{{ $shipping->amount }}</td>
                                <td>
                                   <a href="{{ route('shipping.edit',$shipping->id) }}" class="btn btn-primary">Edit</a>
                                   <a href="javascript:void(0);" onclick="deleterecord({{ $shipping->id  }})" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                           </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
<!--custom js section-->
@section('customjs')
<script>
    $(document).ready(function() {
        $("#shippingForm").submit(function(event){
            event.preventDefault();
            var formData = $(this); 
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("shipping.store") }}',
                type: 'post',
                data: formData.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);
                    if(response['status'] == true){
                        window.location.href = "{{ route('shipping.create') }}";
                        
                    } else {
                        var error = response['errors'];
                    if(error['country']){
                        $('#country').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['country']);
                    } else {
                        $('#country').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(error['amount']){
                        $('#amount').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['amount']);
                    } else {
                        $('#amount').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    } 
                }, 
                error: function(jqXHR, exception){
                    console.log("Something went wrong");
                }
            });
        });
    });

function deleterecord(id){
    var url = '{{ route("shipping.destroy","ID") }}';
    var newurl = url.replace('ID', id);
    if(confirm("Are you sure you want to delete this record?")){
        $.ajax({
            url: newurl,
            type: 'delete',
            data: {},   
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response){
                if(response['status']){
                    window.location.href = "{{ route('shipping.create') }}";
                }
            }
        });
    }
}
    </script>
@endsection