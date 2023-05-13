<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Posting;
use App\Models\PostingLapor;
use App\Models\PostCategory;
use App\Models\PostingAnswer;
use App\Models\PostingReplay;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\TicketDataTable;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class PostinganController extends Controller
{
    public function getSend(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $components['cat'] = PostCategory::get();
        return view('user.posting.send', $components);
    }

    public function postSend(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (strlen($request->message) > 50) {
            if (Auth::user()->point <= $request->point) {
                return response()->json([
                    'status'  => false,
                    'type'    => 'alert',
                    'message' => 'Maaf Point Anda tidak cukup'
                ]);
            } else {
                $user = User::where('id', Auth::user()->id)->first();
                $user->update([
                    'point' => $user->point - $request->point
                ]);

                if ($request->gambar) {
                    $imagepath = '/public/images/';
                    $image_name =  time() . '.' . $request->gambar->extension() . '';
                    $request->gambar->move(getcwd() . $imagepath, $image_name);

                    $gambar =  $image_name;
                } else {
                    $gambar =  "";;
                }

                $input_data = [
                    'user_id'    => Auth::user()->id,
                    'konten'    => $request->message,
                    'category_id' => escape_input($request->category),
                    'point' => escape_input($request->point),
                    'gambar' => $gambar,
                    'ip_address' => $request->ip()
                ];
                $insert_data = Posting::create($input_data);
                return response()->json([
                    'status'  => true,
                    'message' => 'Pertanyaan berhasil dikirim, silahkan menunggu balasan Pengguna.'
                ]);
            }
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Pertanyaan Harus Lebih Dari 50 Karakter'
            ]);
        }
    }

    public function reading(Posting $target)
    {
        if ($target == false) abort('404');
        $components['target'] = $target;
        $baca = array();
        $jsonbaca  = "";
        if ($target->userbaca != "") {

            $bc = json_decode($target->userbaca);
            if (Auth::check() == true) {
                if ($bc < 6) {
                    if (!$this->cekUser($target->userbaca, Auth::user()->id)) {
                        array_push($bc, Auth::user()->id);
                        $jsonbaca = json_encode($bc);
                    }
                }
            }
        } else {
            if (Auth::check() == true) {
                array_push($baca, Auth::user()->id);
                $jsonbaca = json_encode($baca);
            }
        }

        $target->update([
            'baca' => $target->baca + 1,
            'userbaca' =>  $jsonbaca
        ]);
        $components['answer'] = PostingAnswer::where('posting_id', $target->id)->get();
        return view('user.posting.reading', $components);
    }

    public function postTerbaik(Request $request, Posting $target)
    {
        if ($target == false) abort('404');
        //dd($request->id);


        $answer = PostingAnswer::where('id', $request->id)->first();

        $member = User::where('id', $answer->user_id)->first();
        $member->update([
            'point' => $member->point + $target->point,
            'tercerdas' => $member->tercerdas + 1,
        ]);

        $target->update([
            'jawab' => "1"
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Jawaban Terbaik Berhasil Di Pilih'
        ]);
    }

    public function postlapor(Request $request)
    {

        $answer = Posting::where('id', $request->id)->first();
        if ($answer == false) abort('404');

        $input_data = [
            'user_id'             => Auth::user()->id,
            'posting_id'          => $answer->id,
        ];
        $insert_data = PostingLapor::create($input_data);

        return response()->json([
            'status'  => true,
            'message' => 'Laporan Berhasil dikirim'
        ]);
    }

    public function cekUser($msgArray, $id)
    {
        $arr = json_decode($msgArray);
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i] == $id) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function anwser(Posting $target)
    {
        if ($target == false) abort('404');
        if (Auth::check() == true) {
            $components['target'] = $target;
            return view('user.posting.answer', $components);
        } else {
            return redirect('auth/login');
        }
    }

    public function postanwser(Posting $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (strlen($request->jawaban) > 10) {
            if ($request->gambar) {
                $imagepath = '/public/images/';
                $image_name =  time() . '.' . $request->gambar->extension() . '';
                $request->gambar->move(getcwd() . $imagepath, $image_name);

                $gambar =  $image_name;
            } else {
                $gambar =  "";;
            }

            $input_data = [
                'user_id'             => Auth::user()->id,
                'posting_id'          => $target->id,
                'jawaban'              => $request->jawaban,
                'gambar' => $gambar,
                'ip_address'          => $request->ip()
            ];
            $insert_data = PostingAnswer::create($input_data);

            $member = User::where('id', Auth::user()->id)->first();
            $member->update([
                'jawaban' => $member->jawaban + 1,
                'point' => $member->point + website_config('bonus_point')->jawab
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Jawaban Berhasil Di Posting'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Silahkan Masukan Jawaban Dengan Benar'
            ]);
        }
    }

    public function getBycat(PostCategory $category)
    {
        if ($category == false) abort('404');
        $components['category'] = $category;
        $components['listdata'] = Posting::where("category_id", $category->id)->orderBy('id', 'desc')->paginate(10);
        return view('user.posting.category', $components);
    }

    public function pencarian(Request $request)
    {
        $keyword = $request->search;
        $listdata = Posting::where('konten', 'like', "%" . $keyword . "%")->paginate(5);
        return view('user.posting.search', compact('listdata'))->with("keyword", $keyword)->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function postReplay(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (strlen($request->message) > 10) {
            $input_data = [
                'user_id'             => Auth::user()->id,
                'answer_id'          => $request->idjawab,
                'jawaban'              => $request->message,
                'ip_address'          => $request->ip()
            ];
            $insert_data = PostingReplay::create($input_data);
            return response()->json([
                'status'  => true,
                'type'    => 'alert',
                'message' => 'Balasan Berhasil Di Posting'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Invalid Data'
            ]);
        }
    }
}
