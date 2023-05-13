<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Posting;
use App\Models\PostingLapor;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\PostingDataTable;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PostingController extends Controller
{
    public function list(PostingDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Posting',
            'second' => website_config('main')->website_name
        ];
        $components['users'] = Posting::distinct()->latest('user_id')->get(['user_id']);
        $components['created_at'] = Posting::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        $components['updated_at'] = Posting::selectRaw('DATE(updated_at) AS updated_at')->distinct()->latest('updated_at')->get();
        return $dataTable->render('admin.posting.list', $components);
    }

    public function delete(Posting $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->delete()) {
            $ticket_reply = Posting::where('id', $target->id)->delete();
            PostingLapor::where("posting_id", $target->id)->delete();
            return json_encode(['result' => true], JSON_PRETTY_PRINT);
        }
    }

    public function bermasalah()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Posting Bermasalah',
            'second' => website_config('main')->website_name
        ];
        $components['list'] = PostingLapor::where("status", "0")->get();
        return view('admin.posting.bermasalah', $components);
    }
    public function getForm(PostCategory $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        $components['levels'] = ['Member', 'Moderator'];
        return view('admin.posting.form', $components);
    }

    public function postForm(PostCategory $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target->id <> null) {
            $update_data = $target->update([
                "name" => $request->name,
                "slug" => escape_input(Str::slug($request->name))
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'PostCategory berhasil diperbarui.'
            ]);
        } else {
            $input_data = [
                'name'      => escape_input($request->name),
                "slug" => escape_input(Str::slug($request->name))
            ];
            $insert_data = PostCategory::create($input_data);
            return response()->json([
                'status'  => true,
                'message' => 'PostCategory berhasil ditambahkan.'
            ]);
        }
    }
    function kategori()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Kategori Soal',
            'second' => website_config('main')->website_name
        ];
        $components['category'] = PostCategory::get();
        return view('admin.posting.kategori', $components);
    }
}
