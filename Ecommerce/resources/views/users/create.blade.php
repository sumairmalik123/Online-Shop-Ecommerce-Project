@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
        <div class="col-sm-6">
                <h1>Create User</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('users.list')}}" class="btn btn-primary">Back</a>
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
        <form action="" method="post" id="userForm" name="userForm">
            @csrf
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                <p></p>	
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Email</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="Email">
                                
                                <p></p>		
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                                <p></p>		
                            </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone">
                                <p></p>		
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Active</option>
                                <option value="0">Block</option>
                                </select>  
                            </div>
                        </div>
                        										
                    </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{route('users.list')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
<!--custom js section-->
@section('customjs')
<script>
    $(document).ready(function() {
        $("#userForm").submit(function(event){
            event.preventDefault();
            var formData = $(this); 
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("users.store") }}',
                type: 'post',
                data: formData.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);
                    if(response['status'] == true){
                        window.location.href = "{{ route('users.create') }}";
                        //window.location.href = "{{ route('categories.list') }}";
                        $('#name').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#email').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                        $('#phone').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                        $('#password').removeClass('is-invalid').siblings('p')
                    } else {
                        var error = response['error'];
                    if(error['name']){
                        $('#name').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['name']);
                    } else {
                        $('#name').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(error['email']){
                        $('#email').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['email']);
                    } else {
                        $('#email').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(error['phone']){
                        $('#phone').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['phone']);
                    } else {
                        $('#phone').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(error['password']){
                        $('#password').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['password']);
                    } else {
                        $('#password').removeClass('is-invalid').siblings('p')
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

    </script>
@endsection