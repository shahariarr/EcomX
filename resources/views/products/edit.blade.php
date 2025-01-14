@extends('layouts.back')
@section('title', 'Edit Product')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit Product</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('products.index') }}">Products</a>
                </div>
                <div class="breadcrumb-item">Edit Product</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Product</h4>
                            <div class="card-header-form">
                                <a href="{{ route('products.index') }}" class="btn btn-primary my-2"><i
                                        class="bi bi-plus-circle"></i>Back</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('products.update', $product->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="product_name">Product Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="product_name"
                                                        id="product_name" placeholder="Enter Product Name"
                                                        value="{{ old('product_name', $product->product_name) }}" />
                                                    @error('product_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="sku">SKU <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="sku" id="sku"
                                                        placeholder="Enter SKU" value="{{ old('sku', $product->sku) }}" />
                                                    @error('sku')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="model_number">Model Number <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="model_number"
                                                        id="model_number" placeholder="Enter Model Number"
                                                        value="{{ old('model_number', $product->model_number) }}" />
                                                    @error('model_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="price">Price <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="price" id="price"
                                                        step="any" placeholder="Enter Price"
                                                        value="{{ old('price', $product->price) }}" />
                                                    @error('price')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="discount_price">Discount Price <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="discount_price"
                                                        id="discount_price" step="any"
                                                        placeholder="Optional Discount Price"
                                                        value="{{ old('discount_price', $product->discount_price) }}" />
                                                    @error('discount_price')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="stock_quantity">Stock Quantity <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="stock_quantity"
                                                        id="stock_quantity" placeholder="Enter Stock Quantity"
                                                        value="{{ old('stock_quantity', $product->stock_quantity) }}" />
                                                    @error('stock_quantity')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="stock_status">Stock Status <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="stock_status" id="stock_status">
                                                        <option value="In Stock"
                                                            {{ old('stock_status', $product->stock_status) == 'In Stock' ? 'selected' : '' }}>
                                                            In Stock</option>
                                                        <option value="Out of Stock"
                                                            {{ old('stock_status', $product->stock_status) == 'Out of Stock' ? 'selected' : '' }}>
                                                            Out of Stock</option>
                                                        <option value="Pre-order"
                                                            {{ old('stock_status', $product->stock_status) == 'Pre-order' ? 'selected' : '' }}>
                                                            Pre-order</option>
                                                    </select>
                                                    @error('stock_status')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="reorder_level">Reorder Level <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="reorder_level"
                                                        id="reorder_level" placeholder="Enter Reorder Level"
                                                        value="{{ old('reorder_level', $product->reorder_level) }}" />
                                                    @error('reorder_level')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="short_description">Description <span
                                                            class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="short_description" id="short_description" rows="3"
                                                        placeholder="Enter short description">{{ old('short_description', $product->short_description) }}</textarea>
                                                    @error('short_description')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Right Column -->
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">

                                                <div class="form-group">
                                                    <label for="category_id">Category <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="category_id" id="category_id">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="brand_id">Brand <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="brand_id" id="brand_id">
                                                        <option value="">Select Brand</option>
                                                        @foreach ($brands as $brand)
                                                            <option value="{{ $brand->id }}"
                                                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="video">Video URL</label>
                                                    <input type="text" class="form-control" name="video"
                                                        id="video" placeholder="Optional video link"
                                                        value="{{ old('video', $product->video) }}" />
                                                    @error('video')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                                                    <div class="form-group">
                                                        <label for="status">Status</label>
                                                        <select class="form-control" name="status" id="status">
                                                            <option value="active"
                                                                {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                                                                Active
                                                            </option>
                                                            <option value="inactive"
                                                                {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                                                                Inactive</option>
                                                        </select>
                                                        @error('status')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label for="front_view_image">Front Image</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            name="front_view_image" id="front_view_image"
                                                            accept="image/*" />
                                                        <label class="custom-file-label" for="front_view_image">
                                                            Choose file
                                                        </label>
                                                    </div>
                                                    @if ($product->front_view_image)
                                                        <div class="image-preview mt-2" id="frontPreviewContainer">
                                                            <img src="{{ asset($product->front_view_image) }}"
                                                                alt="" id="frontPreview" width="100%" />
                                                        </div>
                                                    @else
                                                        <div class="image-preview mt-2" id="frontPreviewContainer"
                                                            style="display: none;">
                                                            <img src="" alt="" id="frontPreview"
                                                                width="100%" />
                                                        </div>
                                                    @endif
                                                    @error('front_view_image')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="back_view_image">Back Image</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            name="back_view_image" id="back_view_image"
                                                            accept="image/*" />
                                                        <label class="custom-file-label" for="back_view_image">
                                                            Choose file
                                                        </label>
                                                    </div>
                                                    @if ($product->back_view_image)
                                                        <div class="image-preview mt-2" id="backPreviewContainer">
                                                            <img src="{{ asset($product->back_view_image) }}"
                                                                alt="" id="backPreview" width="100%" />
                                                        </div>
                                                    @else
                                                        <div class="image-preview mt-2" id="backPreviewContainer"
                                                            style="display: none;">
                                                            <img src="" alt="" id="backPreview"
                                                                width="100%" />
                                                        </div>
                                                    @endif
                                                    @error('back_view_image')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="side_view_image">Side Image</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            name="side_view_image" id="side_view_image"
                                                            accept="image/*" />
                                                        <label class="custom-file-label" for="side_view_image">
                                                            Choose file
                                                        </label>
                                                    </div>
                                                    @if ($product->side_view_image)
                                                        <div class="image-preview mt-2" id="sidePreviewContainer">
                                                            <img src="{{ asset($product->side_view_image) }}"
                                                                alt="" id="sidePreview" width="100%" />
                                                        </div>
                                                    @else
                                                        <div class="image-preview mt-2" id="sidePreviewContainer"
                                                            style="display: none;">
                                                            <img src="" alt="" id="sidePreview"
                                                                width="100%" />
                                                        </div>
                                                    @endif
                                                    @error('side_view_image')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <a href="{{ route('products.index') }}" class="btn btn-secondary mr-1"><i
                                            class="bi bi-plus-circle"></i>Back</a>
                                    <button type="submit" class="btn btn-primary">Update Product</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#short_description').summernote({
                dialogsInBody: true,
                minHeight: 150,
            });
        });
    </script>
@endpush
