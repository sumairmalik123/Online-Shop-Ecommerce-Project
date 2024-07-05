@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
        <div class="col-sm-6">
                <h1>Change Password</h1>
            </div>
            <div class="col-sm-6 text-right">
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
        <form action="" method="post" id="changePassword" name="changePassword">
            @csrf
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name">Old Password</label>
                                <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password">
                                <p></p>	
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password">
                                <p></p>	
                        </div>
                    </div>
                        <div class="col-md-12">
                           <div class="mb-3">
                            <label for="name">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password">
                            <p></p>	
                        </div>
                     </div>
                  </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{route('categories.list')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
<!--custom js section-->
@section('customjs')
<script>
    $(document).ready(function() {
        $("#changePassword").submit(function(event){
            event.preventDefault();
            var formData = $(this); 
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("account.processChangePassword") }}',
                type: 'post',
                data: formData.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);
                    if(response['status'] == true){
                        window.location.href = "{{ route('account.showChangeAdminPasswordForm') }}";
                        //window.location.href = "{{ route('categories.list') }}";
                        $('#old_password').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#new_password').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#confirm_password').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    } else {
                        var error = response['error'];
                    if(error['old_password']){
                        $('#old_password').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['old_password']);
                    } else {
                        $('#old_password').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(error['new_password']){
                        $('#new_password').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['new_password']);
                    } else {
                        $('#slug').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(error['confirm_password']){
                        $('#confirm_password').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['confirm_password']);
                    } else {
                        $('#confirm_password').removeClass('is-invalid').siblings('p')
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