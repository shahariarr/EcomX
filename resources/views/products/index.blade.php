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
                                <a href="{{ route('products.create') }}" class="btn btn-primary">
                                    + Create Product
                                </a>
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
                                            {{-- <th>Category</th>
                                            <th>Brand</th> --}}
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
            // { data: 'category.name', name: 'category.name' },
            // { data: 'brand.name', name: 'brand.name' },
            { data: 'price', name: 'price' },
            { data: 'stock_quantity', name: 'stock_quantity' },
            { data: 'stock_status', name: 'stock_status' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                return `
                    <a href="{{ url('/') }}/products/${row.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${row.id})">Delete</button>
                `;
            }}
        ]
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
    }
});
</script>
@endpush
