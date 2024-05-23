@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Category</h1>
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
        <form action="" method="post" id="categoryForm" name="categoryForm">
        @csrf
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" value="{{$category->name}}" id="namedata" class="form-control" placeholder="Name">
                                <p></p>	
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" readonly id="slugdata" value="{{$category->slug}}" class="form-control" placeholder="Slug">
                                <p></p>		
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name="image_id" id="image_id" value="" class="form-control">
                                <label for="image">Image</label>
                               <div id="image" class="dropzone dz-clickable">
                                <div class="dz-message needsclick">
                                    <br>Drop files here or click to upload. <br><br>
                                </div>
                               </div>
                            </div>
                            @if (!empty($category->image))
                            <div>
                                <img width="300px" height="300px" src="{{ asset('/uploads/category/' . $category->image) }}" alt="thumbnail">
                            </div>
                        @endif

                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{ $category->status == 1 ? 'selected' : ''}} value="1">Active</option>
                                <option {{ $category->status == 0 ? 'selected' : ''}} value="0">Block</option>
                                </select>  
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Show Home</label>
                                <select name="showHome" id="showHome" class="form-control">
                                    <option {{ $category->showHome == 'Yes' ? 'selected' : ''}} value="Yes">Yes</option>
                                <option {{ $category->showHome == 'No' ? 'selected' : ''}} value="No">No</option>
                                </select>  
                            </div>
                        </div>										
                    </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary" onclick ="return -> route('categories.list')">Update</button>
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
        $("#categoryForm").submit(function(event){
            event.preventDefault();
            var formData = $(this); 
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("categories.update", $category->id) }}',
                type: 'post',
                data: formData.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);
                    if(response['status'] == true){
                        window.location.href = "{{route('categories.edit', $category->id )}}";
                        $('#namedata').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#slugdata').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    } else {
                        if(response['not'] == 'error'){
                            
                        }
                        var errors = response['errors'];
                    if(errors['name']){
                        $('#namedata').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors['name']);
                    } else {
                        $('#namedata').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(errors['slug']){
                        $('#slugdata').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors['slug']);
                    } else {
                        $('#slugdata').removeClass('is-invalid').siblings('p')
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
        $("#namedata").on('change', function(){
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
                        $('#slugdata').val(response["slug"]);
                    }
                }
            });
        });
    }); 
//dropzone image
    Dropzone.autoDiscover = false;
const dropzone = new Dropzone('#image', {
    init: function() {
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
                this.removeFile(this.files[0]);
            }
        });
    },
    url: "{{ route('temp-images.create') }}",
    maxFiles: 1,
    paramName: "image",
    acceptedFiles: 'image/jpeg, image/png, image/gif',
    addRemoveLinks: true,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
    },
    success: function(file, response) {
        $("#image_id").val(response.image_id);
    },
    error: function(file, response) {
        // Handle error
    }
});

    </script>
@endsection