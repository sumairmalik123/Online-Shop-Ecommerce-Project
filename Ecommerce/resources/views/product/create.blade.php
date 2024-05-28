@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Product</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route("products.list") }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <form action="" method="" id="productForm" name="productForm">
    @csrf
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Title">
                                    <p class="error"></p>	
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug">
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Short Description</label>
                                    <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote" placeholder="short_descrition"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Shipping and Returns</label>
                                    <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="summernote" placeholder="shipping resturns"></textarea>
                                </div>
                            </div>                                            
                        </div>
                    </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Media</h2>								
                        <div id="image" class="dropzone dz-clickable">
                            <div class="dz-message needsclick">    
                                <br>Drop files here or click to upload.<br><br>                                            
                            </div>
                        </div>
                    </div>
                    <div class="row" id="product_images">
                        </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Pricing</h2>								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="price">Price</label>
                                    <input type="text" name="price" id="price" class="form-control" placeholder="Price">
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="compare_price">Compare at Price</label>
                                    <input type="text" name="compare_price" id="compare_price" class="form-control" placeholder="Compare Price">
                                    <p class="text-muted mt-3">
                                        To show a reduced price, move the product’s original price into Compare at price. Enter a lower value into Price.
                                    </p>	
                                </div>
                            </div>                                            
                        </div>
                    </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Inventory</h2>								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" id="sku" class="form-control" placeholder="sku">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barcode">Barcode</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Barcode">	
                                </div>
                            </div>   
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="track_qty" value ="No">
                                        <input class="custom-control-input  " type="checkbox" id="track_qty" name="track_qty" value="Yes" checked>
                                        
                                        <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="number" min="0" name="qty" id="qty" class="form-control" placeholder="Qty">	
                                    <p class="error"></p>
                                </div>
                            </div>                                         
                        </div>
                    </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Related product</h2>
                        <div class="mb-3">
                            <select multiple class="related-products w-100" name="related_products[]" id="related_products">
                            
                            </select>
                            <p class="error"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Product status</h2>
                        <div class="mb-3">
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Block</option>
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card">
                    <div class="card-body">	
                        <h2 class="h4  mb-3">Product category</h2>
                        <div class="mb-3">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">Select a category</option>
                                @if ($categories->isNotEmpty())
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="mb-3">
                            <label for="category">Sub category</label>
                            <select name="sub_category" id="sub_category" class="form-control">
                                <option value="">Select a sub category</option>
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Product brand</h2>
                        <div class="mb-3">
                            <select name="brand" id="brand" class="form-control">
                                <option value="">Select a category</option>
                                @if ($brands->isNotEmpty())
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            @endif
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Featured product</h2>
                        <div class="mb-3">
                            <select name="is_featured" id="is_featured" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>                                                
                            </select>
                            <p class="error"></p>
                        </div>
                    </div>
                </div>                                 
            </div>
        </div>
        
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route("products.list") }}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </div>
    </form>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection
<!--custom js section-->
@section('customjs')
<script>
//select2
$('.related-products').select2({
    ajax: {
        url: '{{ route("products.getproducts") }}',
        dataType: 'json',
        tags: true,
        multiple: true,
        minimumInputLength: 3,
        processResults: function (data) {
            return {
                results: data.tags
            }
        }
    }
});


   $(document).ready(function() {
    $("#productForm").submit(function(event){
        event.preventDefault(); // Prevent default form submission behavior      
        var formData = $(this); // Serialize form data       
        $("button[type=submit]").prop('disabled', true); // Disable submit button        
        $.ajax({
            url: '{{ route("products.store") }}', // URL to submit form data
            type: 'post', // HTTP method
            data: formData.serializeArray(), // Serialized form data
            dataType: 'json', // Expected data type of the response
            success: function(response){ // Success callback function
                // Enable submit button
                $("button[type=submit]").prop('disabled', false);               
                // Handle response data
                if (response['status'] == true){
                    //window.location.replace = '{{ route("products.list") }}';
                    // If status is true, clear any existing error messages
                    $(".error").removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('is-invalid');
                } else {
                    // If status is false, display validation errors
                    var errors = response['errors'];
                    $(".error").removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('is-invalid');
                    $.each(errors,function(key,value){
                        $(`#${key}`).addClass('is-invalid')
                            .siblings('p')
                            .addClass("invalid-feedback").html(value);
                    });
                }                   
            }, 
            error: function(jqXHR, exception){ // Error callback function
                console.log("Something went wrong"); // Log error message
            }
        });
    });
});

//data slug
    $(document).ready(function(){
        $("#title").on('change', function(){
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
//dropzone image
    Dropzone.autoDiscover = false;
const dropzone = new Dropzone('#image', {
    url: "{{ route('temp-images.create') }}",
    maxFiles: 10,
    paramName: "image",
    acceptedFiles: 'image/jpeg, image/png, image/gif',
    addRemoveLinks: true,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
    },
    success: function(file, response) {
        //$("#image_id").val(response.image_id);

        var html = `<div class="col-md-3" id="image-${response.image_id}">
                <div class="card">
                    <img class="card-img-top" src="${response.imagepath}" alt="">
                    <input type="hidden" name="product_images[]" value="${response.image_id}">
                    <div class="card-body">
                        <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-primary delete-image-btn" data-image-id="${response.image_id}">Delete</a>
                    </div>
                </div>
            </div>`;
            // Append the HTML to the product_images container
            $("#product_images").append(html);
    },
    complete: function(file) {
        this.removeFile(file);
    }
});
$("#category").change(function() {
    var category_id = $(this).val();
    $.ajax({
        url: "{{ route('product-subcatogries.index') }}",
        type: 'get',
        data: {category_id: category_id},
        dataType: 'json',
        success: function(response) {
            $("#sub_category").find("option").not(":first").remove();
            $.each(response.subCategories, function(key, item) { 
                $("#sub_category").append('<option value="' + item.id + '">' + item.name + '</option>'); 
            });
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText); 
        }
    });
});

function deleteImage(image_id) {
    $("#image-" + image_id).remove();
}

    </script>
@endsection