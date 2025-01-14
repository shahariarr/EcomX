@extends('layouts.back')
@section('title', 'Manage Products')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Manage Products</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">Products</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Products List</h4>
                            <div class="card-header-form">
                                <button
                                    class="btn btn-primary"
                                    data-toggle="modal"
                                    data-target="#modelId"
                                >
                                    Create Product
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table
                                    class="table"
                                    id="data-table"
                                    style="width: 100%"
                                >
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Image</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Price</th>
                                            <th>Stock Quantity</th>
                                            <th>Stock Status</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('modals')
<div
    class="modal fade"
    id="modelId"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modelTitleId"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create Product</h5>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id" id="productId" />
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input
                            type="text"
                            class="form-control"
                            name="product_name"
                            id="product_name"
                            placeholder="Enter Product Name"
                        />
                    </div>
                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input
                            type="text"
                            class="form-control"
                            name="sku"
                            id="sku"
                            placeholder="Enter SKU"
                        />
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select
                            class="form-control"
                            name="category_id"
                            id="category_id"
                        >
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand_id">Brand</label>
                        <select
                            class="form-control"
                            name="brand_id"
                            id="brand_id"
                        >
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="model_number">Model Number</label>
                        <input
                            type="text"
                            class="form-control"
                            name="model_number"
                            id="model_number"
                            placeholder="Enter Model Number"
                        />
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input
                            type="number"
                            class="form-control"
                            name="price"
                            id="price"
                            step="any"
                            placeholder="Enter Price"
                        />
                    </div>
                    <div class="form-group">
                        <label for="discount_price">Discount Price</label>
                        <input
                            type="number"
                            class="form-control"
                            name="discount_price"
                            id="discount_price"
                            step="any"
                            placeholder="Optional Discount Price"
                        />
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input
                            type="number"
                            class="form-control"
                            name="stock_quantity"
                            id="stock_quantity"
                            placeholder="Enter Stock Quantity"
                        />
                    </div>
                    <div class="form-group">
                        <label for="stock_status">Stock Status</label>
                        <select
                            class="form-control"
                            name="stock_status"
                            id="stock_status"
                        >
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                            <option value="Pre-order">Pre-order</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reorder_level">Reorder Level</label>
                        <input
                            type="number"
                            class="form-control"
                            name="reorder_level"
                            id="reorder_level"
                            placeholder="Enter Reorder Level"
                        />
                    </div>
                    <div class="form-group">
                        <label for="short_description">Short Description</label>
                        <textarea
                            class="form-control"
                            name="short_description"
                            id="short_description"
                            rows="3"
                            placeholder="Enter short description"
                        ></textarea>
                    </div>
                    <div class="form-group">
                        <label for="video">Video URL</label>
                        <input
                            type="text"
                            class="form-control"
                            name="video"
                            id="video"
                            placeholder="Optional video link"
                        />
                    </div>
                    @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="front_view_image">Front Image</label>
                        <div class="custom-file">
                            <input
                                type="file"
                                class="custom-file-input"
                                name="front_view_image"
                                id="front_view_image"
                                accept="image/*"
                            />
                            <label
                                class="custom-file-label"
                                for="front_view_image"
                            >
                                Choose file
                            </label>
                        </div>
                        <div
                            class="image-preview mt-2"
                            id="frontPreviewContainer"
                            style="display: none;"
                        >
                            <img src="" alt="" id="frontPreview" width="100%" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="back_view_image">Back Image</label>
                        <div class="custom-file">
                            <input
                                type="file"
                                class="custom-file-input"
                                name="back_view_image"
                                id="back_view_image"
                                accept="image/*"
                            />
                            <label
                                class="custom-file-label"
                                for="back_view_image"
                            >
                                Choose file
                            </label>
                        </div>
                        <div
                            class="image-preview mt-2"
                            id="backPreviewContainer"
                            style="display: none;"
                        >
                            <img src="" alt="" id="backPreview" width="100%" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="side_view_image">Side Image</label>
                        <div class="custom-file">
                            <input
                                type="file"
                                class="custom-file-input"
                                name="side_view_image"
                                id="side_view_image"
                                accept="image/*"
                            />
                            <label
                                class="custom-file-label"
                                for="side_view_image"
                            >
                                Choose file
                            </label>
                        </div>
                        <div
                            class="image-preview mt-2"
                            id="sidePreviewContainer"
                            style="display: none;"
                        >
                            <img src="" alt="" id="sidePreview" width="100%" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        orderable: true,
        ajax: "{{ route('products.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'product_name', name: 'product_name' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'category.name', name: 'category.name' }, // Ensure this matches the structure
            { data: 'brand.name', name: 'brand.name' },
            { data: 'price', name: 'price' },
            { data: 'stock_quantity', name: 'stock_quantity' },
            { data: 'stock_status', name: 'stock_status' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    function previewFile(input, previewId, containerId) {
        var file = input.files[0];
        $(input).next('.custom-file-label').html(file.name);
        var reader = new FileReader();
        reader.onload = function(e) {
            $(previewId).attr('src', e.target.result);
            $(containerId).show();
        }
        reader.readAsDataURL(file);
    }

    $('#front_view_image').on('change', function() {
        previewFile(this, '#frontPreview', '#frontPreviewContainer');
    });

    $('#back_view_image').on('change', function() {
        previewFile(this, '#backPreview', '#backPreviewContainer');
    });

    $('#side_view_image').on('change', function() {
        previewFile(this, '#sidePreview', '#sidePreviewContainer');
    });

    $('#modelId').on('hidden.bs.modal', function() {
        resetForm();
    });

    $("#productForm").on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var id = $('#productId').val();
        var url = id
            ? "{{ url('/') }}" + '/products/' + id
            : "{{ route('products.store') }}";

        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status) {
                    iziToast.success({
                        title: 'Success',
                        timeout: 1500,
                        message: 'Product has been saved!',
                        position: 'topRight'
                    });
                    $('#modelId').modal('hide');
                    table.draw();
                    resetForm();
                } else {
                    iziToast.error({
                        title: 'Error',
                        timeout: 1500,
                        message: data.message,
                        position: 'topRight'
                    });
                }
            },
            error: function(err) {
                if (err.status === 422) {
                    var errors = err.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        iziToast.error({
                            title: 'Error',
                            timeout: 1500,
                            message: value[0],
                            position: 'topRight'
                        });
                    });
                } else {
                    iziToast.error({
                        title: 'Error',
                        timeout: 1500,
                        message: 'Something went wrong, please try again later',
                        position: 'topRight'
                    });
                }
            }
        });
    });

    function resetForm() {
        $('#productForm')[0].reset();
        $('#frontPreview, #backPreview, #sidePreview').attr('src', '');
        $('.image-preview').hide();
        $('#modelId').find('.modal-title').text('Create Product');
        $('#submit').text('Submit');
        $('#productForm').attr('action', '{{ route('products.store') }}');
        $('#productForm').attr('method', 'POST');
        $('#productId').val('');
        $("#front_view_image").next('.custom-file-label').html('Choose file');
        $("#back_view_image").next('.custom-file-label').html('Choose file');
        $("#side_view_image").next('.custom-file-label').html('Choose file');
    }
});

function deleteProduct(id) {
    var token = $("meta[name='csrf-token']").attr("content");
    var url = "{{ url('/') }}" + '/products/' + id;

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type: "DELETE",
                url: url,
                data: {
                    "_token": token,
                },
                success: function (data) {
                    if (data.status) {
                        iziToast.success({
                            title: 'Deleted',
                            timeout: 1500,
                            message: 'Product successfully deleted',
                            position: 'topRight'
                        });
                        $('#data-table').DataTable().ajax.reload();
                    } else {
                        iziToast.error({
                            title: 'Error',
                            timeout: 1500,
                            message: data.message,
                            position: 'topRight'
                        });
                    }
                },
                error: function (err) {
                    iziToast.error({
                        title: 'Error',
                        timeout: 1500,
                        message: 'Something went wrong, please try again later',
                        position: 'topRight'
                    });
                }
            });
        }
    });
};

function editProduct(id) {
    var url = "{{ url('/') }}" + '/products/' + id + '/edit';
    $.ajax({
        type: "GET",
        url: url,
        success: function (data) {
            if (data.status) {
                $('#productId').val(data.data.id);
                $('#product_name').val(data.data.product_name);
                $('#sku').val(data.data.sku);
                $('#category_id').val(data.data.category_id);
                $('#brand_id').val(data.data.brand_id);
                $('#model_number').val(data.data.model_number);
                $('#price').val(data.data.price);
                $('#discount_price').val(data.data.discount_price);
                $('#stock_quantity').val(data.data.stock_quantity);
                $('#stock_status').val(data.data.stock_status);
                $('#reorder_level').val(data.data.reorder_level);
                $('#short_description').val(data.data.short_description);
                $('#video').val(data.data.video);

                if ($('#status').length) {
                    $('#status').val(data.data.status);
                }

                if (data.data.front_view_image) {
                    $('#frontPreview').attr('src', "{{ asset('/') }}" + data.data.front_view_image);
                    $('#frontPreviewContainer').show();
                }
                if (data.data.back_view_image) {
                    $('#backPreview').attr('src', "{{ asset('/') }}" + data.data.back_view_image);
                    $('#backPreviewContainer').show();
                }
                if (data.data.side_view_image) {
                    $('#sidePreview').attr('src', "{{ asset('/') }}" + data.data.side_view_image);
                    $('#sidePreviewContainer').show();
                }

                $('#modalTitle').text('Edit Product');
                $('#submit').text('Save changes');
                $('#productForm').attr('action', "{{ url('/') }}" + '/products/' + data.data.id);
                $('#productForm').attr('method', 'POST');
                $('#modelId').modal('show');
            } else {
                iziToast.error({
                    title: 'Error',
                    timeout: 1500,
                    message: 'Failed to fetch product data',
                    position: 'topRight'
                });
            }
        },
        error: function (err) {
            iziToast.error({
                title: 'Error',
                timeout: 1500,
                message: 'Something went wrong, please try again later',
                position: 'topRight'
            });
        }
    });
}
</script>
@endpush
