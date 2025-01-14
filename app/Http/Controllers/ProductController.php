<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            if (auth()->user()->hasRole('Super Admin')) {
                $data = Product::with('category', 'brand')->get();
            } else {
                $data = Product::with('category', 'brand')
                    ->where('user_id', operator: auth()->user()->id)
                    ->get();
            }

            return datatables($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex">';
                    $btn .= '<button onclick="editProduct(' . $row->id . ')" class="btn btn-primary btn-sm mr-2"><i class="bi bi-pencil-square"></i> Edit</button>';
                    $btn .= '<button onclick="deleteProduct(' . $row->id . ')" class="btn btn-danger btn-sm mr-2"><i class="bi bi-trash"></i> Delete</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('image', function ($data) {
                    return '<img src="' . asset($data->front_view_image) . '" width="70px"/>';
                })
                ->addColumn('product_name', function ($data) {
                    return $data->product_name;
                })
                ->addColumn('category', function ($data) {
                    return $data->category->name;
                })
                ->addColumn('brand', function ($data) {
                    return $data->brand->name;
                })
                ->addColumn('price', function ($data) {
                    return $data->price;
                })
                ->addColumn('stock_quantity', function ($data) {
                    return $data->stock_quantity;
                })
                ->addColumn('stock_status', function ($data) {
                    return $data->stock_status;
                })
                ->addColumn('status', function ($data) {
                    $badgeClass = $data->status == 'active' ? 'bg-primary' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($data->status) . '</span>';
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }

        return view('products.index', [
            'categories' => Category::all(),
            'brands' => Brand::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('products.create', [
            'categories' => Category::all(),
            'brands' => Brand::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name'      => 'required|string|max:255',
            'sku'               => 'required|string',
            'category_id'       => 'required|integer',
            'brand_id'          => 'required|integer',
            'model_number'      => 'nullable|string|max:255',
            'price'             => 'required|numeric',
            'discount_price'    => 'nullable|numeric',
            'stock_quantity'    => 'nullable|integer',
            'stock_status'      => 'nullable|string|max:255',
            'reorder_level'     => 'nullable|integer',
            'short_description' => 'nullable|string',
            'front_view_image'  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'back_view_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'side_view_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'video'             => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = auth()->user()->hasRole(['Admin', 'Super Admin']) ? 'active' : 'inactive';
        $imagePathFront = null;
        $imagePathBack = null;
        $imagePathSide = null;
        $path = 'storage/product';

        try {
            if ($request->hasFile('front_view_image')) {
                $imageName = auth()->user()->id . '_front_' . time() . '.' . $request->front_view_image->extension();
                $request->front_view_image->move(public_path($path), $imageName);
                $imagePathFront = $path . '/' . $imageName;
            }
            if ($request->hasFile('back_view_image')) {
                $imageName = auth()->user()->id . '_back_' . time() . '.' . $request->back_view_image->extension();
                $request->back_view_image->move(public_path($path), $imageName);
                $imagePathBack = $path . '/' . $imageName;
            }
            if ($request->hasFile('side_view_image')) {
                $imageName = auth()->user()->id . '_side_' . time() . '.' . $request->side_view_image->extension();
                $request->side_view_image->move(public_path($path), $imageName);
                $imagePathSide = $path . '/' . $imageName;
            }

            Product::create([
                'product_name'      => $request->product_name,
                'sku'               => $request->sku,
                'category_id'       => $request->category_id,
                'brand_id'          => $request->brand_id,
                'model_number'      => $request->model_number,
                'slug'              => slug($request->product_name),
                'status'            => $status,
                'price'             => $request->price,
                'discount_price'    => $request->discount_price,
                'stock_quantity'    => $request->stock_quantity,
                'stock_status'      => $request->stock_status,
                'reorder_level'     => $request->reorder_level,
                'short_description' => $request->short_description,
                'front_view_image'  => $imagePathFront,
                'back_view_image'   => $imagePathBack,
                'side_view_image'   => $imagePathSide,
                'video'             => $request->video,
                'user_id' => auth()->user()->id,
            ]);

            return response()->json(['status' => true, 'message' => 'Product created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        $product = Product::find($id);
        $categories = Category::all();
        $brands = Brand::all();

        if ($product) {
            return response()->json([
                'status' => true,
                'data' => $product,
                'categories' => $categories,
                'brands' => $brands
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Product not found']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'product_name'      => 'required|string|max:255',
            'sku'               => 'sometimes|string',
            'category_id'       => 'sometimes|integer',
            'brand_id'          => 'sometimes|integer',
            'model_number'      => 'nullable|string|max:255',
            'price'             => 'sometimes|numeric',
            'discount_price'    => 'nullable|numeric',
            'stock_quantity'    => 'nullable|integer',
            'stock_status'      => 'nullable|string|max:255',
            'reorder_level'     => 'nullable|integer',
            'short_description' => 'nullable|string',
            'front_view_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'back_view_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'side_view_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'video'             => 'nullable|string'
        ];

        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $rules['status'] = 'required|in:active,inactive';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = 'storage/product';

        try {
            $product = Product::findOrFail($id);

            $imagePathFront = $product->front_view_image;
            $imagePathBack = $product->back_view_image;
            $imagePathSide = $product->side_view_image;

            if ($request->hasFile('front_view_image')) {
                if ($imagePathFront && file_exists(public_path($imagePathFront))) {
                    unlink(public_path($imagePathFront));
                }
                $imageName = auth()->user()->id . '_front_' . time() . '.' . $request->front_view_image->extension();
                $request->front_view_image->move(public_path($path), $imageName);
                $imagePathFront = $path . '/' . $imageName;
            }
            if ($request->hasFile('back_view_image')) {
                if ($imagePathBack && file_exists(public_path($imagePathBack))) {
                    unlink(public_path($imagePathBack));
                }
                $imageName = auth()->user()->id . '_back_' . time() . '.' . $request->back_view_image->extension();
                $request->back_view_image->move(public_path($path), $imageName);
                $imagePathBack = $path . '/' . $imageName;
            }
            if ($request->hasFile('side_view_image')) {
                if ($imagePathSide && file_exists(public_path($imagePathSide))) {
                    unlink(public_path($imagePathSide));
                }
                $imageName = auth()->user()->id . '_side_' . time() . '.' . $request->side_view_image->extension();
                $request->side_view_image->move(public_path($path), $imageName);
                $imagePathSide = $path . '/' . $imageName;
            }

            $updateData = [
                'product_name'      => $request->product_name,
                'sku'               => $request->has('sku') ? $request->sku : $product->sku,
                'category_id'       => $request->has('category_id') ? $request->category_id : $product->category_id,
                'brand_id'          => $request->has('brand_id') ? $request->brand_id : $product->brand_id,
                'model_number'      => $request->model_number,
                'price'             => $request->has('price') ? $request->price : $product->price,
                'discount_price'    => $request->discount_price,
                'stock_quantity'    => $request->stock_quantity,
                'stock_status'      => $request->stock_status,
                'reorder_level'     => $request->reorder_level,
                'short_description' => $request->short_description,
                'front_view_image'  => $imagePathFront,
                'back_view_image'   => $imagePathBack,
                'side_view_image'   => $imagePathSide,
                'video'             => $request->video,
                'slug'              => slug($request->product_name),
                'user_id' => auth()->user()->id,
            ];

            if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
                $updateData['status'] = $request->status;
            }

            $product->update($updateData);

            return response()->json(['status' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $paths = [
                $product->front_view_image,
                $product->back_view_image,
                $product->side_view_image
            ];
            foreach ($paths as $file) {
                if ($file && file_exists(public_path($file))) {
                    unlink(public_path($file));
                }
            }
            $product->delete();
            return response()->json(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
