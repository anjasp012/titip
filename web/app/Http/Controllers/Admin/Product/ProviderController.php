<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ProductProvider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\DataTables\Admin\Product\ProviderDataTable;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProviderController extends Controller {
    public function list(ProviderDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Penyedia',
			'second' => 'Produk'
		];
        $components['statuses'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        return $dataTable->render('admin.product.provider.list', $components);
    }
    public function getForm(ProductProvider $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        return view('admin.product.provider.form', $components);
    }
    public function postForm(ProductProvider $target, PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'name' 	 	           => escape_input($request->name),
            'provider_url_order'   => $request->provider_url_order,
            'provider_url_status'  => $request->provider_url_status,
            'provider_url_service' => $request->provider_url_service,
            'provider_id'          => $request->provider_id,
            'provider_key'         => $request->provider_key,
            'provider_secret'      => $request->provider_secret,
		];
		if ($target->id <> null) {
			$check_data = ProductProvider::where([['name', $input_data['name']]])->first();
			if ($input_data['name'] <> $target['name'] AND $check_data) {
				$validator = Validator::make($request->all(), [
					'name' => 'required|unique:service_providers,name|max:20',
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
				'message' => 'Penyedia berhasil diperbarui.'
			]);
		} else {
			$insert_data = ProductProvider::create($input_data);
			return response()->json([
				'status'  => true, 
				'message' => 'Penyedia berhasil ditambahkan.'
			]);
		}
	}
	public function status(ProductProvider $target, $status, Request $request) {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if (Arr::exists(['0', '1'], $status) == false) return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        $update_data = $target->update(['status' => $status]);
        if ($update_data == true) {
            return (json_encode([
                'result'  => true,
                'message' => 'Status Penyedia <b>'.$target->full_name.'</b> berhasil diperbarui.'
            ], JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        }
	}
    public function delete(ProductProvider $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) {
            $product = Product::where('provider_id', $target->id)->delete();
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
				'name' 	 	           => 'required|max:30|unique:product_providers,name',
				'provider_url_order'   => 'required|url|max:50',
				'provider_url_status'  => 'required|url|max:50',
				'provider_url_service' => 'required|url|max:50',
				'provider_id'          => 'required',
				'provider_key'         => 'required',
				'provider_secret'      => 'max:200',
			];
		}
		return [
            'name' 	 	           => 'required|max:30',
            'provider_url_order'   => 'required|url|max:50',
            'provider_url_status'  => 'required|url|max:50',
            'provider_url_service' => 'required|url|max:50',
            'provider_id'          => 'required',
            'provider_key'         => 'required',
            'provider_secret'      => 'max:200',
		];
    }
    public function attributes() {
		return [
            'name' 	 	           => 'Nama',
            'provider_url_order'   => 'URL Pemesanan',
            'provider_url_status'  => 'URL Status',
            'provider_url_service' => 'URL Layanan',
            'provider_id'          => 'ID',
            'provider_key'         => 'Kunci',
            'provider_secret'      => 'Rahasia',
		];
    }
}
