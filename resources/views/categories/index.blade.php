@extends('layouts.back')
@section('title', 'Manage Categories')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Manage Categories</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Categories</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Categories List</h4>
                            <div class="card-header-form">
                                @can('create-categorie')
                                    <a href="javascript:void(0)" class="btn btn-success btn-sm my-2" data-toggle="modal"
                                        data-target="#modelId"><i class="bi bi-plus-circle"></i>+ Add New Category</a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="data-table" class="table dataTable no-footer table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
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
<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="categoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id" id="categoryId">
                    <div class="form-group">
                        <label for="name">Title</label>
                        <input type="text" class="form-control" name="name" id="name" aria-describedby="helpId" placeholder="Enter Title">
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                            <label class="custom-file-label" for="image">Choose file</label>
                        </div>
                        <div class="image-preview mt-2" style="display: none">
                            <img src="" alt="" id="preview" width="100%">
                        </div>
                    </div>
                    @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
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
            ajax: "{{ route('categories.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'created_by', name: 'created_by' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#image').on('change', function() {
            var file = this.files[0];
            $("#image").next('.custom-file-label').html(file.name);
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
                $('.image-preview').show();
            }
            reader.readAsDataURL(file);
        });

        $('#modelId').on('hidden.bs.modal', function() {
            resetForm();
        });

        $("#categoryForm").on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var id = $('#categoryId').val();
            var url = id ? "{{ url('/') }}" + '/categories/' + id : "{{ route('categories.store') }}";
            var type = id ? "POST" : "POST";

            if (id) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                type: type,
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.status) {
                        iziToast.success({
                            title: 'Success',
                            timeout: 1500,
                            message: data.message,
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
                    console.log(err.responseJSON);
                    if (err.status === 422) {
                        var errors = err.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            iziToast.error({
                                title: 'Error',
                                timeout: 1500,
                                message: value,
                                position: 'topRight'
                            });
                        });
                    } else {
                        iziToast.error({
                            title: 'Error',
                            timeout: 1500,
                            message: 'Something went wrong. Please try again later',
                            position: 'topRight'
                        });
                    }
                }
            });
        });

        function resetForm() {
            $('#categoryForm')[0].reset();
            $('#preview').attr('src', '');
            $('.image-preview').hide();
            $('#modelId').find('.modal-title').text('Create Category');
            $('#submit').text('Submit');
            $('#categoryForm').attr('action', '{{ route('categories.store') }}');
            $('#categoryForm').attr('method', 'POST');
            $('#categoryId').val('');
            $("#image").next('.custom-file-label').html('Choose file');
        }
    });

    function deleteCategory(id) {
        var token = $("meta[name='csrf-token']").attr("content");
        var url = "{{ url('/') }}" + '/categories/' + id;

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
                                title: 'Success',
                                timeout: 1500,
                                message: data.message,
                                position: 'topRight'
                            });
                            $('#data-table').DataTable().ajax.reload(); // Reload the DataTable
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
                            message: 'Something went wrong. Please try again later',
                            position: 'topRight'
                        });
                    }
                });
            }
        });
    };

    function editCategory(id) {
        var url = "{{ url('/') }}" + '/categories/' + id + '/edit';

        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                if (data.status) {
                    // Populate the edit form with the category data
                    $('#categoryId').val(data.data.id);
                    $('#name').val(data.data.name);
                    $('#status').val(data.data.status);
                    $('#modalTitle').text('Edit Category');
                    $('#submit').text('Save changes');
                    $('#categoryForm').attr('action', "{{ url('/') }}" + '/categories/' + data.data.id);
                    $('#categoryForm').attr('method', 'POST');
                    $('#modelId').modal('show');

                    // Set the image preview
                    if (data.data.image) {
                        $('#preview').attr('src', "{{ asset('/') }}" + data.data.image);
                        $('.image-preview').show();
                    }
                } else {
                    iziToast.error({
                        title: 'Error',
                        timeout: 1500,
                        message: 'Failed to fetch category data',
                        position: 'topRight'
                    });
                }
            },
            error: function (err) {
                iziToast.error({
                    title: 'Error',
                    timeout: 1500,
                    message: 'Something went wrong. Please try again later',
                    position: 'topRight'
                });
            }
        });
    }
</script>
@endpush
