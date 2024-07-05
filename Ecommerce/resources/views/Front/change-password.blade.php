@extends('Front.layout.app')
@section('content')

<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
    <div class="light-font">
    <ol class="breadcrumb primary-color mb-0">
    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
    <li class="breadcrumb-item">Change Password</li>
    </ol>
    </div>
    </div>
    </section>
<section class=" section-11 ">
<div class="container mt-5">
<div class="row">
    <div class="col-md-12">
        @if (Session::has('success'))
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {!! Session:: get('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>            </div> 
            @endif
            @if (Session::has('error'))
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session:: get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>            </div> 
            @endif
    </div>
<div class="col-md-3">
@include('Front/account/common/sidebar')
</div>
<div class="col-md-9">
    <div class="card">
        <div class="card-header">
            <h2 class="h5 mb-0 pt-2 pb-2">Change Password</h2>
        </div>
        <form action="" method="post" name="changePasswordForm" id="changePasswordForm">
        <div class="card-body p-4">
            <div class="row">
                <div class="mb-3">               
                    <label for="name">Old Password</label>
                    <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control">
                    <p></p>
                </div>
                <div class="mb-3">               
                    <label for="name">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                    <p></p>
                </div>
                <div class="mb-3">               
                    <label for="name">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="form-control">
                    <p class="text-danger"></p>
                </div>
                <div class="d-flex">
                    <button id="submit" name="submit" type="submit" class="btn btn-dark">Save</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
</div>
</div>
</section>
@endsection
       
<!--custom js section-->
@section('customjs')
<script type="text/javascript">
$("#changePasswordForm").submit(function(event){
    event.preventDefault();
    var formData = $(this);
    $("#submit").prop('disabled', true);
    $.ajax({
        url: "{{ route('user.changePasswordproecess') }}",
        type: 'post',
        data: formData.serialize(),
        dataType: 'json',
        success: function (response) {
            $("#submit").prop('disabled', false);
            if (response.status == true) {
               window.location.href="{{ route('user.changePassword') }}"
            } else {
                var errors = response.error;
                if (errors.old_password) {
                    $("#changePasswordForm #old_password").addClass('is-invalid').siblings('p').html(errors.old_password).addClass('invalid-feedback');
                } else {
                    $("#changePasswordForm #old_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');
                }
                if (errors.new_password) {
                    $("#changePasswordForm #new_password").addClass('is-invalid').siblings('p').html(errors.new_password).addClass('invalid-feedback');
                } else {
                    $("#changePasswordForm #new_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');
                }
                if (errors.confirm_password) {
                    $("#changePasswordForm #confirm_password").addClass('is-invalid').siblings('p').html(errors.confirm_password).addClass('invalid-feedback');
            } else {
                $("#changePasswordForm #confirm_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');
            }
        }
        }
    });
});
/*$(document).ready(function(){
    $("#changePasswordForm").validate({
        rules: {
            old_password: "required",
            new_password: "required",
            confirm_password: "required",
        },
        messages: {
            old_password: "Please enter old password",
            new_password: "Please enter new password",
            confirm_password: "Please enter confirm password",
        }
    });
})*/

</script>
@endsection