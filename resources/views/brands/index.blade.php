@extends('layouts.back')
@section('title', 'Manage Brands')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Manage Brands</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Brands</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Brands List</h4>
                            <div class="card-header-form">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modelId"> +Add Brand</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
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
                <h5 class="modal-title" id="modalTitle">Create Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="brandForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id" id="brandId">
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
                                <option value="active">Active</option>
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
            ajax: "{{ route('brands.index') }}",
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

        $("#brandForm").on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var id = $('#brandId').val();
            var url = id ? "{{ url('/') }}" + '/brands/' + id : "{{ route('brands.store') }}";
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
                                message: value[0],
                                position: 'topRight'
                            });
                        });
                    } else {
                        iziToast.error({
                            title: 'Error',
                            timeout: 1500,
                            position: 'topRight'
                        });
                    }
                }
            });
        });

        function resetForm() {
            $('#brandForm')[0].reset();
            $('#preview').attr('src', '');
            $('.image-preview').hide();
            $('#modelId').find('.modal-title').text('Create Brand');
            $('#submit').text('Submit');
            $('#brandForm').attr('action', '{{ route('brands.store') }}');
            $('#brandForm').attr('method', 'POST');
            $('#brandId').val('');
            $("#image").next('.custom-file-label').html('Choose file');
        }
    });

    function deleteBrand(id) {
        var token = $("meta[name='csrf-token']").attr("content");
        var url = "{{ url('/') }}" + '/brands/' + id;

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
                            $('#data-table').DataTable().ajax.reload(); // Reload the DataTable
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: data.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (err) {
                        iziToast.error({
                            title: 'Error',
                            position: 'topRight'
                        });
                    }
                });
            }
        });
    };

    function editBrand(id) {
        var url = "{{ url('/') }}" + '/brands/' + id + '/edit';

        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                if (data.status) {
                    $('#brandId').val(data.data.id);
                    $('#name').val(data.data.name);
                    $('#status').val(data.data.status);
                    $('#modalTitle').text('Edit Brand');
                    $('#submit').text('Save changes');
                    $('#brandForm').attr('action', "{{ url('/') }}" + '/brands/' + data.data.id);
                    $('#brandForm').attr('method', 'POST');
                    $('#modelId').modal('show');

                    if (data.data.image) {
                        $('#preview').attr('src', "{{ asset('/') }}" + data.data.image);
                        $('.image-preview').show();
                    }
                } else {
                    iziToast.error({
                        title: 'Error',
                        timeout: 1500,
                        message: 'Failed to fetch brand data',
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
