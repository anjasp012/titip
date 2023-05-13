<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Order;
use App\Models\Product;
use App\Models\CustomPrice;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductProvider;
use App\Models\ProductSubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\DataTables\Admin\Service\ServiceDataTable;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductController extends Controller {
    public function list(Request $request) {
        $config['category_id'] = '0';
        if (request('category_id') <> '' AND request('category_id') <> '') {
            if (is_numeric(request('category_id')) == true) {
                $config['category_id'] = request('category_id');
            } 
        }
        $components['table'] = Product::join('product_categories', 'products.category_id', '=', 'product_categories.id')->join('product_sub_categories', 'products.sub_category_id', '=', 'product_sub_categories.id')->select('products.*', 'product_categories.name AS category', 'product_sub_categories.name AS sub_category')->when(request(), function ($query) use ($request) {
            if (request('category_id') <> '' AND request('category_id') <> '') {
                if (is_numeric(request('category_id')) == true) {
                    $query->where('products.category_id', request('category_id'));
                } 
            }
            if (request('sub_category_id') <> '' AND request('sub_category_id') <> '') {
                if (is_numeric(request('sub_category_id')) == true) {
                    $query->where('products.sub_category_id', request('sub_category_id'));
                } 
            }
            if (request('search_value') <> '' AND request('search_value') <> '') {
                $query ->where('products.name', 'like', '%'.htmlspecialchars(strip_tags(request('search_value'))).'%');
            }
        })->get();
    	$components = [
            'categories' 	  => ProductCategory::orderBy('id', 'desc')->get(),
            'sub_categories'  => ProductSubCategory::where('category_id', $config['category_id'])->get(),
    		'table' 		  => $components['table'],
        ];
        $components['breadcrumb'] = (object) [
			'first'  => 'Daftar Produk',
			'second' => 'Produk'
        ];
    	return view('admin.product.product.list', $components);
    }
    public function getForm(Product $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
		$components['categories'] = ProductCategory::where('status', '1')->latest('id')->get();
		$components['sub_categories'] = ProductSubCategory::where('status', '1')->latest('id')->get();
		$components['providers'] = ProductProvider::where('status', '1')->latest('id')->get();
        return view('admin.product.product.form', $components);
    }
    public function postForm(Product $target, PostRequest $request) {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'category_id'         => escape_input($request->category_id),
            'sub_category_id'     => escape_input($request->sub_category_id),
            'name'                => escape_input($request->name),
            'agen_price'          => fixed_amount($request->agen_price),
            'reseller_price'      => fixed_amount($request->reseller_price),
            'profit'              => fixed_amount($request->profit),
            'bonus'               => fixed_amount($request->bonus),
            'provider_id'         => escape_input($request->provider_id),
            'provider_product_id' => escape_input($request->provider_product_id),
		];
		if ($target->id <> null) {
			$check_data = Product::where([['name', $input_data['name']]])->first();
			if ($input_data['name'] <> $target['name'] AND $check_data) {
				$validator = Validator::make($request->all(), [
					'name' => 'required|unique:products,name|max:100',
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
				'message' => 'Produk berhasil diperbarui.'
			]);
		} else {
			$insert_data = Product::create($input_data);
            return response()->json([
				'status'  => true, 
				'message' => 'Produk berhasil ditambahkan.'
			]);
		}
	}
	public function status(Product $target, $status) {
        if (Auth::guard('admin')->user()->level == 'Admin') {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $target = Product::findOrFail($target->id);
        if (Arr::exists(['0', '1'], $status) == false) abort('404');
        $update_data = $target->update(['status' => $status]);
        if ($update_data == true) {
            return redirect()->back()->with('result', [
                'alert'   => 'success', 
                'title'   => 'Berhasil', 
                'message' => 'Status berhasil diperbarui.'
            ]);
        } else {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Terjadi kesalahan.'
            ]);
        }
	}
	public function delete(Product $target) {
		if (request()->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) return json_encode(['result' => true], JSON_PRETTY_PRINT);
	}
	public function detail(Product $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        return view('admin.product.product.detail', compact('target'));
    }
}

class PostRequest extends FormRequest {
    protected function prepareForValidation() {
		$this->merge([
			'agen_price'     => $this->agen_price <> '' ? fixed_amount($this->agen_price) : '',
			'reseller_price' => $this->reseller_price <> '' ? fixed_amount($this->reseller_price) : '',
			'bonus'          => $this->bonus <> '' ? fixed_amount($this->bonus) : '',
			'profit'         => $this->profit <> '' ? fixed_amount($this->profit) : '',
		]);
	}
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
    public function rules() {
		if (request()->segment(4) == null) {
			return [
				'category_id'         => 'required|numeric|exists:product_categories,id',
				'sub_category_id'     => 'required|numeric|exists:product_sub_categories,id',
				'provider_id'         => 'required|numeric|exists:product_providers,id',
				'provider_product_id' => 'required|max:30|unique:products,provider_product_id',
				'name'                => 'required|unique:products,name',
				'agen_price'          => 'required|numeric|integer|min:0',
				'reseller_price'      => 'required|numeric|integer|min:0',
				'profit'              => 'required|numeric|integer|min:0',
				'bonus'               => 'required|numeric|integer|min:0',
			];
		}
		return [
            'category_id'         => 'required|numeric|exists:product_categories,id',
            'sub_category_id'     => 'required|numeric|exists:product_sub_categories,id',
            'provider_id'         => 'required|numeric|exists:product_providers,id',
            'provider_product_id' => 'required|max:30',
            'name'                => 'required',
            'agen_price'          => 'required|numeric|integer|min:0',
            'reseller_price'      => 'required|numeric|integer|min:0',
            'profit'              => 'required|numeric|integer|min:0',
            'bonus'               => 'required|numeric|integer|min:0',
		];
    }
    public function attributes() {
		return [
            'category_id'         => 'Kategori',
            'sub_category_id'     => 'Sub Kategori',
            'provider_id'         => 'Penyedia',
            'provider_product_id' => 'ID Produk Penyedia',
            'name'                => 'Nama',
            'agen_price'          => 'Harga Agen',
            'reseller_price'      => 'Harga Reseller',
            'profit'              => 'Keuntungan',
            'bonus'               => 'Bonus',
		];
    }
}
