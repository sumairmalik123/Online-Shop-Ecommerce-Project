@extends('Front.layout.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Register</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="login-form">    
                <form action="" method="post" name="registrationForm" id="registrationForm">
                    <h4 class="modal-title">Register Now</h4>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name" id="name" name="name">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Email" id="email" name="email">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation">
                        <p></p>
                    </div>
                    <div class="form-group small">
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div> 
                    <button type="submit" class="btn btn-dark btn-block btn-lg" value="Register">Register</button>
                </form>			
                <div class="text-center small">Already have an account? <a href="{{ route('user.login') }}">Login Now</a></div>
            </div>
        </div>
    </section>

@endsection
@section('customjs')
<script>
  $(document).ready(function() {
    $("#registrationForm").submit(function(event) {
        event.preventDefault();
        var formData = $(this);
        $("button[type=submit]").prop('disabled', true);

        $.ajax({
            url: '{{ route("user.processRegister") }}',
            type: 'post',
            data: formData.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled', false);
                var errors = response['error'];
                if (response['status'] == false) {
                    if (errors['name']) {
                        $('#name').addClass('is-invalid').siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors['name']);
                    } else {
                        $('#name').removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                    if (errors['email']) {
                        $('#email').addClass('is-invalid').siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors['email']);
                    } else {
                        $('#email').removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                    if (errors['password']) {
                        $('#password').addClass('is-invalid').siblings('p') // assuming Bootstrap class
                            .addClass('invalid-feedback')
                            .html(errors['password']);
                    } else {
                        $('#password').removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                } else {
                    window.location.href = "{{ route('user.login') }}";
                    $('#name').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    $('#email').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    $('#password').removeClass('is-invalid').siblings('p') // assuming Bootstrap class
                        .removeClass('invalid-feedback')
                        .html("");
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error:", jqXHR, exception); // Log specific error details
            }
        });
    });
});

</script>
@endsection
