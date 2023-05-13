<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\WebsiteInformation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\DataTables\Admin\Settings\WebsiteInformationDataTable;

class WebsiteInformationController extends Controller
{
	public function list(WebsiteInformationDataTable $dataTable)
	{
		$components['breadcrumb'] = (object) [
			'first' => 'Daftar Informasi',
			'second' => 'Pengaturan'
		];
		$components['categories'] = ['Info', 'Maintenance', 'Update', 'Product', 'Service', 'Other'];
		$components['created_at'] = WebsiteInformation::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
		return $dataTable->render('admin.settings.website_information.list', $components);
	}
	public function getForm(WebsiteInformation $target, Request $request)
	{
		if ($request->ajax() == false) abort('404');
		if ($target == true) $components['target'] = $target;
		$components['categories'] = ['Info', 'Maintenance', 'Update', 'Product', 'Service', 'Other'];
		return view('admin.settings.website_information.form', $components);
	}
	public function postForm(WebsiteInformation $target, PostRequest $request)
	{
		if ($request->ajax() == false) abort('404');
		if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false,
				'type'    => 'alert',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}
		$input_data = [
			'category' => escape_input($request->category),
			'title'  => $request->title,
			'content'  => $request->content,
			'is_popup'    => "0",
			/*'is_popup'    => is_null($request->is_popup) ? '0' : '1'*/
		];
		if ($target->id <> null) {
			$update_data = $target->update($input_data);
			return response()->json([
				'status'  => true,
				'message' => 'Informasi berhasil diperbarui.'
			]);
		} else {
			$insert_data = WebsiteInformation::create($input_data);
			return response()->json([
				'status'  => true,
				'message' => 'Informasi berhasil ditambahkan.'
			]);
		}
	}
	public function delete(WebsiteInformation $target, Request $request)
	{
		if ($request->ajax() == false) abort('404');
		if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) {
			return json_encode(['result' => true], JSON_PRETTY_PRINT);
		}
	}
}

class PostRequest extends FormRequest
{
	protected function getValidatorInstance()
	{
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
	public function rules(Request $request)
	{
		return [
			'category' => 'required|in:Info,Maintenance,Update,Product,Service,Other',
			'content'  => 'required',
		];
	}
	public function attributes()
	{
		return [
			'category' => 'Kategori',
			'content'  => 'Konten',
		];
	}
}
