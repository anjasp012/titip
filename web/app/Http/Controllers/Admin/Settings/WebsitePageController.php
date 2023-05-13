<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\WebsitePage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\DataTables\Admin\Settings\WebsitePageDataTable;

class WebsitePageController extends Controller {
    public function list(WebsitePageDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Halaman',
			'second' => 'Pengaturan'
		];
        $components['created_at'] = WebsitePage::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.settings.website_page.list', $components);
    }
    public function getForm(WebsitePage $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        return view('admin.settings.website_page.form', $components);
    }
    public function postForm(WebsitePage $target, PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'title'   => escape_input($request->title),
            'slug'    => Str::slug($request->slug),
            'content' => $request->content,
		];
		if ($target->id <> null) {
            $check_data = WebsitePage::where([['title', $input_data['title']]])->first();
			if ($input_data['title'] <> $target['title'] AND $check_data) {
				$validator = Validator::make($request->all(), [
					'title' => 'required|unique:website_pages,title|max:20',
				], [], ['title' => 'Judul']);
				if ($validator->fails()) {
					return response()->json([
						'status'  => false, 
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
            }
            $check_data = WebsitePage::where([['slug', $input_data['slug']]])->first();
			if ($input_data['slug'] <> $target['slug'] AND $check_data) {
				$validator = Validator::make($request->all(), [
					'slug' => 'required|unique:website_pages,slug|max:20',
				], [], ['slug' => 'Slug']);
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
				'message' => 'Halaman berhasil diperbarui.'
			]);
		} else {
			$insert_data = WebsitePage::create($input_data);
			return response()->json([
				'status'  => true, 
				'message' => 'Halaman berhasil ditambahkan.'
			]);
		}
	}
	public function delete(WebsitePage $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) {
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
    public function rules() {
        if (request()->segment(5) == null) {
			return [
                'title'   => 'required|max:30|unique:website_pages,title',
                'slug'    => 'required|max:40|unique:website_pages,slug',
                'content' => 'required',
			];
		}
		return [
            'title'   => 'required|max:30',
            'slug'    => 'required|max:40',
            'content' => 'required',
		];
    }
    public function attributes() {
		return [
            'title'   => 'Judul',
            'slug'    => 'Slug',
            'content' => 'Konten',
		];
    }
}
