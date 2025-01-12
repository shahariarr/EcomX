<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Brand::with('user')->get();

            return datatables($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<div class="d-flex">';
                        $btn .= '<button onclick="editBrand('.$row['id'].')" class="btn btn-primary btn-sm mr-2"><i class="bi bi-pencil-square"></i> Edit</button>';
                        $btn .= '<button onclick="deleteBrand('.$row['id'].')" class="btn btn-danger btn-sm mr-2"><i class="bi bi-trash"></i> Delete</button>';
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->addColumn('image', function($data) {
                        return '<img src="'.asset($data->image).'" width="70px"/>';
                    })
                    ->addColumn('created_by', function($data) {
                        return $data->user->name;
                    })
                    ->addColumn('status', function($data) {
                        $badgeClass = $data->status == 'active' ? 'bg-primary' : 'bg-secondary';
                        return '<span class="badge '.$badgeClass.'">'.ucfirst($data->status).'</span>';
                    })
                    ->rawColumns(['action', 'image', 'status'])
                    ->make(true);
        }

        return view('brands.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine status based on user role
        $status = auth()->user()->hasRole(['Admin', 'Super Admin']) ? 'active' : 'inactive';

        // Store image
        try {
            $path = 'storage/brand';
            if ($request->hasFile('image')) {
                $image_name = auth()->user()->id . time() . '.' . $request->image->extension();
                $request->image->move(public_path($path), $image_name);
                $image_path = $path . '/' . $image_name;
            }

            // Store brand
            $brand = Brand::create([
                'name' => $request->name,
                'slug' => slug($request->name),
                'image' => $image_path,
                'user_id' => auth()->user()->id,
                'status' => $status
            ]);

            return response()->json(['status' => true, 'message' => 'Brand created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            return response()->json(['status' => true, 'data' => $brand]);
        } else {
            return response()->json(['status' => false, 'message' => 'Brand not found']);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $rules = [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        // Store image
        try {
            $brand = Brand::find($id);
            $path = 'storage/brand';
            if ($request->hasFile('image')) {
                $old_image = $brand->image;
                if (file_exists(public_path($old_image))) {
                    unlink(public_path($old_image));
                }
                $image_name = auth()->user()->id . time() . '.' . $request->image->extension();
                $request->image->move(public_path($path), $image_name);
                $image_path = $path . '/' . $image_name;
            } else {
                $image_path = $brand->image;
            }

            // Update brand
            $updateData = [
                'name' => $request->name,
                'slug' => slug($request->name),
                'image' => $image_path,
                'user_id' => auth()->user()->id,
            ];

            if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
                $updateData['status'] = $request->status;
            }

            $brand->update($updateData);

            return response()->json(['status' => true, 'message' => 'Brand updated successfully']);
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
            $brand = Brand::find($id);
            $path = $brand->image;
            if (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
            $brand->delete();
            return response()->json(['status' => true, 'message' => 'Brand deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}