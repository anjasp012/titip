<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use App\Models\CustomPrice;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\DataTables\Admin\Product\SubCategoryDataTable;

class SubCategoryController extends Controller {
    public function list(SubCategoryDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Sub Kategori',
			'second' => 'Produk'
		];
        $components['categories'] = ProductSubCategory::distinct()->latest('category_id')->get(['category_id']);
        $components['statuses'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        return $dataTable->render('admin.product.sub_category.list', $components);
    }
    public function getForm(ProductSubCategory $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        $components['categories'] = ProductCategory::where('status', '1')->orderBy('id', 'desc')->get();
        return view('admin.product.sub_category.form', $components);
    }
    public function postForm(ProductSubCategory $target, PostRequest $request) {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'category_id' => escape_input($request->category_id),
            'name'        => escape_input($request->name),
			'slug'        => escape_input(Str::slug($request->name)),
		];
		if ($target->id <> null) {
			$check_data = ProductSubCategory::where([
                ['name', $input_data['name']],
                ['category_id', $input_data['category_id']]
            ])->first();
			if ($input_data['name'] <> $target['name'] OR $input_data['category_id'] <> $target['category_id'] AND $check_data) {
				$validator = Validator::make($request->all(), [
					'name' => 'required|unique:sub_categories,name|max:20',
				], [], ['name' => 'Nama']);
				if ($validator->fails()) {
                    return response()->json([
						'status'  => false, 
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
			}
			$update_data = $target->update($input_data);
			return response()->json([
				'status'  => true, 
				'message' => 'Sub Kategori berhasil diperbarui.'
			]);
		} else {
			$check_data = ProductSubCategory::where([
                ['name', $input_data['name']],
                ['category_id', $input_data['category_id']]
            ])->first();
			if ($check_data) {
				$validator = Validator::make($request->all(), [
					'name' => 'required|unique:sub_categories,name|max:20',
				], [], ['name' => 'Nama']);
				if ($validator->fails()) {
                    return response()->json([
						'status'  => false, 
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
			}
			$insert_data = ProductSubCategory::create($input_data);
			return response()->json([
				'status'  => true, 
				'message' => 'Sub Kategori berhasil ditambahkan.'
			]);
		}
	}
	public function status(ProductSubCategory $target, $status, Request $request) {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if (Arr::exists(['0', '1'], $status) == false) return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        $update_data = $target->update(['status' => $status]);
        if ($update_data == true) {
            return (json_encode([
                'result'  => true,
                'message' => 'Status Sub Kategori <b>'.$target->full_name.'</b> berhasil diperbarui.'
            ], JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        }
	}
	public function delete(ProductSubCategory $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) {
            $product = Product::where('sub_category_id', $target->id)->delete();
			return json_encode(['result' => true], JSON_PRETTY_PRINT);
		}
	}
}

class PostRequest extends FormRequest {
	protected function getValidatorInstance() {
		$instance = parent::getValidatorInstance();
        if ($instance->fails() == true) {
			throw new HttpResponseException(response()->json([
				'status'  => false, 
				'type'    => 'validation',
				'message' => parent::getValidatorInstance()->errors()
			]));
		}
        return parent::getValidatorInstance();
    }
    public function rules(Request $request) {
		if (request()->segment(5) == null) {
			return [
				'category_id' => 'required|numeric|exists:product_categories,id',
				'name'        => 'required',
			];
		}
		return [
            'category_id' => 'required',
            'name'        => 'required',
		];
    }
    public function attributes() {
		return [
            'category_id' => 'Kategori',
            'name'        => 'Nama',
		];
    }
}
