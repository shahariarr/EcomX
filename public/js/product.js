$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#data-table')) {
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            orderable: true,
            ajax: "/products",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'product_name', name: 'product_name' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'category.name', name: 'category.name' },
                { data: 'brand.name', name: 'brand.name' },
                { data: 'price', name: 'price' },
                { data: 'stock_quantity', name: 'stock_quantity' },
                { data: 'stock_status', name: 'stock_status' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

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
            ? "/products/" + id
            : "/products";

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
        $('#productForm').attr('action', '/products');
        $('#productForm').attr('method', 'POST');
        $('#productId').val('');
        $("#front_view_image").next('.custom-file-label').html('Choose file');
        $("#back_view_image").next('.custom-file-label').html('Choose file');
        $("#side_view_image").next('.custom-file-label').html('Choose file');
    }
});

function deleteProduct(id) {
    var token = $("meta[name='csrf-token']").attr("content");
    var url = "/products/" + id;

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
    var url = "/products/" + id + "/edit";
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
                    $('#frontPreview').attr('src', "/" + data.data.front_view_image);
                    $('#frontPreviewContainer').show();
                }
                if (data.data.back_view_image) {
                    $('#backPreview').attr('src', "/" + data.data.back_view_image);
                    $('#backPreviewContainer').show();
                }
                if (data.data.side_view_image) {
                    $('#sidePreview').attr('src', "/" + data.data.side_view_image);
                    $('#sidePreviewContainer').show();
                }

                $('#modalTitle').text('Edit Product');
                $('#submit').text('Save changes');
                $('#productForm').attr('action', "/products/" + data.data.id);
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
