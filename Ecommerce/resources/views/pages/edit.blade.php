@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
        <div class="col-sm-6">
                <h1>Update Page</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('pages.list')}}" class="btn btn-primary">Back</a>
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
        <form action="" method="post" id="pageForm" name="pageForm">
            @csrf
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input value="{{ $pages->name }}" type="text" name="name" id="name" class="form-control" placeholder="Name">
                                <p></p>	
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input value="{{ $pages->slug }}" type="text" name="slug" readonly id="slug" class="form-control" placeholder="Slug">
                                <p></p>		
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="content">Content</label>
                                <textarea  name="content" id="content" class="summernote" cols="30" rows="10">{!! $pages->content !!}</textarea>
                              
                            </div>
                        </div>
                       										
                    </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{route('pages.list')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
<!--custom js section-->
@section('customjs')
<script>
    //summernote
    $('.summernote').summernote({
        placeholder: 'Content',
        tabsize: 2,
        height: 250
    });
    $(document).ready(function() {
        $("#pageForm").submit(function(event){
            event.preventDefault();
            var formData = $(this); 
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("pages.update",$pages->id) }}',
                type: 'post',
                data: formData.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);
                    if(response['status'] == true){
                        window.location.href = "{{ route('pages.list') }}";
                        //window.location.href = "{{ route('categories.list') }}";
                        $('#name').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#slug').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
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
                    if(error['slug']){
                        $('#slug').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(error['slug']);
                    } else {
                        $('#slug').removeClass('is-invalid').siblings('p')
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
//data slug
    $(document).ready(function(){
        $("#name").on('change', function(){
            element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("getSlug") }}',
                type: 'get',
                data: {title: element.val()},
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        $('#slug').val(response["slug"]);
                    }
                }
            });
        });
    }); 


    </script>
@endsection