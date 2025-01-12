<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->hasRole('Super Admin')) {
                $data = Category::with('user')->get();
            } elseif (auth()->user()->hasRole('Admin')) {
                $data = Category::with('user')->where('user_id', auth()->user()->id)->get();
            } elseif (auth()->user()->hasRole('User')) {
                $data = Category::with('user')->where('user_id', auth()->user()->id)->get();
            } else {
                $data = null;
            }

            return datatables($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<div class="d-flex">';
                        $btn .= '<button onclick="editCategory('.$row['id'].')" class="btn btn-primary btn-sm mr-2"><i class="bi bi-pencil-square"></i> Edit</button>';
                        $btn .= '<button onclick="deleteCategory('.$row['id'].')" class="btn btn-danger btn-sm mr-2"><i class="bi bi-trash"></i> Delete</button>';
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

        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
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
            $path = 'storage/category';
            if ($request->hasFile('image')) {
                $image_name = auth()->user()->id . time() . '.' . $request->image->extension();
                $request->image->move(public_path($path), $image_name);
                $image_path = $path . '/' . $image_name;
            }

            // Store category
            $category = Category::create([
                'name' => $request->name,
                'slug' => slug($request->name),
                'image' => $image_path,
                'user_id' => auth()->user()->id,
                'status' => $status
            ]);

            return response()->json(['status' => true, 'message' => 'Category created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json(['status' => true, 'data' => $category]);
        } else {
            return response()->json(['status' => false, 'message' => 'Category not found']);
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
            $category = Category::find($id);
            $path = 'storage/category';
            if ($request->hasFile('image')) {
                $old_image = $category->image;
                if (file_exists(public_path($old_image))) {
                    unlink(public_path($old_image));
                }
                $image_name = auth()->user()->id . time() . '.' . $request->image->extension();
                $request->image->move(public_path($path), $image_name);
                $image_path = $path . '/' . $image_name;
            } else {
                $image_path = $category->image;
            }

            // Update category
            $updateData = [
                'name' => $request->name,
                'slug' => slug($request->name),
                'image' => $image_path,
                'user_id' => auth()->user()->id,
            ];

            if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
                $updateData['status'] = $request->status;
            }

            $category->update($updateData);

            return response()->json(['status' => true, 'message' => 'Category updated successfully']);
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
            $category = Category::find($id);
            $path = $category->image;
            if (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
            $category->delete();
            return response()->json(['status' => true, 'message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
