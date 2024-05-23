@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Sub Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('subcategories.list')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
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
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="subcategoryForm" name="subcategoryForm">
            @csrf
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="name">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">Select Category</option>
                                @if ($categories->isNotEmpty())
                                    @foreach ($categories as $category)
                                    <option {{ $subcategory->category_id == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="{{ $subcategory->name }}" class="form-control" placeholder="Name">
                            <p></p>	
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug">Slug</label>
                            <input type="text" readonly name="slug" value="{{ $subcategory->slug }}" id="slug" class="form-control" placeholder="Slug">	
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status">status</label>
                            <select name="status" id="status" class="form-control">
                                <option {{ $subcategory->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                <option {{ $subcategory->status == 0 ? 'selected' : '' }} value="0">Block</option>
                            </select>
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status">Show Home</label>
                            <select name="showHome" id="showHome" class="form-control">
                                <option {{ $subcategory->showHome == 'Yes' ? 'selected' : '' }} value="Yes">Yes</option>
                            <option {{ $subcategory->showHome == 'No' ? 'selected' : '' }} value="No">No</option>
                            </select>  
                        </div>
                    </div>									
                </div>
            </div>							
        </div>
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('subcategories.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection
<!--custom js section-->
@section('customjs')
<script>
    //form store & validation
    $(document).ready(function() {
        $("#subcategoryForm").submit(function(event){
            event.preventDefault();
            var formData = $(this); 
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("subcategories.update", $subcategory->id) }}',
                type: 'post',
                data: formData.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);
                    if(response['status'] == 'true'){
                        window.location.href = "{{ route('subcategories.list') }}";
                        $('#name').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#slug').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                        $('#category').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    } else {
                        if(response['notfound'] = "true"){
                            window.location.href = "{{ route('subcategories.list') }}";
                            return false;
                        }
                        var errors = response['errors'];
                    if(errors['name']){
                        $('#name').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors['name']);
                    } else {
                        $('#name').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(errors['slug']){
                        $('#slug').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors['slug']);
                    } else {
                        $('#slug').removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    }
                    if(errors['category']){
                        $('#category').addClass('is-invalid').siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors['category']);
                    } else {
                        $('#category').removeClass('is-invalid').siblings('p')
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
    //get slug
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