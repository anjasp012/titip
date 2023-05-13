<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectBid;
use App\Models\ProjectBidMessage;
use App\Models\UserBalanceLog;
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

class ProjectController extends Controller
{
    public function list(Request $request)
    {
        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        $components['project'] = Project::where("user_id", Auth::user()->id)->get();
        return view('user.project.list', $components);
    }

    public function browseproject()
    {
        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        $components['project'] = Project::where("user_id", "!=", Auth::user()->id)->where("project_status", "Active")->get();
        return view('user.project.browse', $components);
    }

    public function createproject()
    {
        $components['breadcrumb'] = (object) [
            'first'  => "Create Project",
            'second' => 'Halaman'
        ];
        $components['cat'] = ProjectCategory::get();
        return view('user.project.create', $components);
    }

    public function postcreate(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        /*if (Auth::user()->balance < $request->budgetto) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Maaf Saldo Anda tidak mencukupi'
            ]);
        }*/
        if (strlen($request->desc) > 50) {
            $input_data = [
                'user_id'    => Auth::user()->id,
                'title'    => escape_input($request->title),
                'deskripsi'    => $request->desc,
                'project_status' => "Pending",
                'category_id' => escape_input($request->kategori),
                'estimasi' => escape_input($request->finishday),
                'budget_from' => fixed_amount($request->budgetfrom),
                'budget_to' => fixed_amount($request->budgetto)
            ];
            $insert_data = Project::create($input_data);
            /*$check_user = User::find(Auth::user()->id);
            $cut_balance = $check_user->update([
                'balance' => Auth::user()->balance - $request->budgetto,
            ]);

            $balance_logs = UserBalanceLog::create([
                'user_id'     => Auth::user()->id,
                'order_id'    => $insert_data->id,
                'type'        => 'Minus',
                'action'      => 'Project',
                'amount'      => $request->budgetto,
                'description' => 'Membuat Project #' . $insert_data->id . '.',
            ]);
            */
            return response()->json([
                'status'  => true,
                'message' => 'Project Berhasil Di Kirim'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Deskripsi Harus Lebih Dari 50 Karakter'
            ]);
        }
    }

    function detail(Project $target)
    {
        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        $components['target'] = $target;
        $components['isbid'] = false;
        $components['projectbid'] = ProjectBid::where("project_id", $target->id)->get();

        $cek = ProjectBid::where("project_id", $target->id)->where("user_id", Auth::user()->id)->first();
        if ($cek) {
            $components['isbid'] = true;
        }
        return view('user.project.detail', $components);
    }

    public function projectbid(Project $target)
    {
        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        $components['target'] = $target;
        return view('user.project.bid', $components);
    }

    public function postbid(Project $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $input_data = [
            'user_id'    => Auth::user()->id,
            'project_id'    => $target->id,
            'amount'    => fixed_amount($request->amount),
        ];

        $target->update([
            "bidcount" => $target->bidcount + 1
        ]);
        $insert_data = ProjectBid::create($input_data);

        $input_datames = [
            'user_id'    => Auth::user()->id,
            'project_id'    => $target->id,
            'message' => $request->desc
        ];
        ProjectBidMessage::create($input_datames);

        return response()->json([
            'status'  => true,
            'message' => 'Project Bid'
        ]);
    }


    public function bidplaced()
    {
        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        return view('user.project.bidplaced', $components);
    }

    public function bidhistory()
    {
        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        $components['project'] = ProjectBid::where("user_id", Auth::user()->id)->get();
        return view('user.project.bidhistory', $components);
    }

    public function cancelbid(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $cek = ProjectBid::where("project_id", $request->dataid)->where("user_id", Auth::user()->id)->first();
        if ($cek) {
            $cek->update([
                "status" => "Cancel"
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Update Bid Berhasil'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Update Bid Gagal'
            ]);
        }
    }

    public function show_conversation(ProjectBid $target)
    {

        if ($target->user_id == Auth::user()->id) {
            $cek = ProjectBid::where("project_id", $target->project_id)->where("status", "Approve")->orWhere("status", "Gugatan")->orWhere("status", "Ended")->first();
        } else {
            $cek = Project::where("id", $target->project_id)->where("user_id", Auth::user()->id)->where("project_status", "Start")->orWhere("project_status", "Ended")->first();
        }


        if ($cek == false) abort(404);
        if ($cek) {
            $components['isbid'] = true;
        }
        $components['target'] = $target;
        $components['project'] = Project::where("id", $target->project_id)->first();
        $components['chat'] = ProjectBidMessage::where("project_id", $target->project_id)->orderBy('id', 'desc')->get();
        return view('user.project.show_conversation', $components);
    }

    public function chat(ProjectBid $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target->user_id == Auth::user()->id) {
            $cek = ProjectBid::where("project_id", $target->project_id)->where("status", "Approve")->first();
        } else {
            $cek = Project::where("id", $target->project_id)->where("user_id", Auth::user()->id)->where("project_status", "Start")->first();
        }

        if ($cek == false) abort(404);
        $input_datames = [
            'user_id'    => Auth::user()->id,
            'project_id'    => $target->project_id,
            'message' => $request->jawaban
        ];
        ProjectBidMessage::create($input_datames);

        return response()->json([
            'status'  => true,
            'message' => 'Project Bid'
        ]);
    }

    public function postgugatan(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $cek = ProjectBid::where("id", $request->dataid)->where("status", "Approve")->first();
        if ($cek == false) abort(404);
        if (($cek->user_id == Auth::user()->id) or ($cek->project->user_id == Auth::user()->id)) {
            $cek->update([
                "status" => "Gugatan"
            ]);

            $input_datames = [
                'user_id'    => Auth::user()->id,
                'project_id'    => $cek->project_id,
                'message' => $request->msg,
                'tipe' => "1"
            ];
            ProjectBidMessage::create($input_datames);

            return response()->json([
                'status'  => true,
                'message' => 'Gugatan berhasil di kirim'
            ]);
        } else {
            abort(404);
        }
    }

    public function userfinish(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $cek = ProjectBid::where("id", $request->dataid)->where("status", "Approve")->first();
        if ($cek == false) abort(404);
        if ($cek->user_id != Auth::user()->id) {
            abort(404);
        }
        $cek->update([
            "status" => "Finish"
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Project Berhasil di update'
        ]);
    }


    public function show_bid(Project $target)
    {
        $cek = Project::where("id", $target->id)->where("user_id", Auth::user()->id)->first();
        if ($cek == false) abort(404);
        $components['target'] = $target;
        $components['projectbid'] = ProjectBid::where("project_id", $target->id)->get();
        return view('user.project.show_bid', $components);
    }

    public function cancelproject(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $cek = Project::where("id", $request->dataid)->where("user_id", Auth::user()->id)->first();
        if ($cek) {
            /*
            $balance_logs = UserBalanceLog::create([
                'user_id'     => $cek->user_id,
                'order_id'    => $cek->id,
                'type'        => 'Plus',
                'action'      => 'Project',
                'amount'      => $cek->budget_to,
                'description' => 'Refund Project #' . $cek->id . '.',
            ]);
            */
            $cek->update([
                "project_status" => "Cancel"
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Update Project Berhasil'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Update Project Gagal'
            ]);
        }
    }

    public function finishproject(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $pbid = ProjectBid::where("id", $request->dataid)->first();
        $cek = Project::where("id", $pbid->project_id)->where("user_id", Auth::user()->id)->first();
        $user = User::where("id", $pbid->user_id)->first();
        if ($cek) {

            $pbid->update([
                "status" => "Ended"
            ]);

            $cek->update([
                "project_status" => "Ended"
            ]);

            $user->update([
                "current_project" => $user->current_project - 1,
                "balance" => $user->balance + $pbid->amount
            ]);

            $balance_logs = UserBalanceLog::create([
                'user_id'     => $user->id,
                'order_id'    => $cek->id,
                'type'        => 'Plus',
                'action'      => 'Project',
                'amount'      => $pbid->amount,
                'description' => 'Fee Project #' . $cek->id . '.',
            ]);

            $text = "Tugas dengan judul (" . $cek->title . ") telah selesai dan kedua belah pihak telah menyepakatinya. Terima kasih menggunakan jasa TitipTugas.com dan kami tunggu untuk tugas selanjutnya.";
            send_watsapp($user->phone_number, $text);

            /*$kurang = $cek->budget_to - $pbid->amount;
            if ($kurang > 0) {
                $balance_logs = UserBalanceLog::create([
                    'user_id'     => $user->id,
                    'order_id'    => $cek->id,
                    'type'        => 'Plus',
                    'action'      => 'Project',
                    'amount'      => $cek->budget_to,
                    'description' => 'Refund Project #' . $cek->id . '.',
                ]);
            }*/


            $cek->update([
                "project_status" => "Ended"
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Update Bid Berhasil'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Update Bid Gagal'
            ]);
        }
    }

    public function approvebid(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $pbid = ProjectBid::where("id", $request->dataid)->first();
        $cek = Project::where("id", $pbid->project_id)->where("user_id", Auth::user()->id)->first();
        $user = User::where("id", $pbid->user_id)->first();
        if (Auth::user()->balance < $pbid->amount) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Maaf Saldo Anda tidak mencukupi,Silahkan Lakukan deposit terlebih dahulu'
            ]);
        }

        if ($cek) {

            $check_user = User::find(Auth::user()->id);
            $cut_balance = $check_user->update([
                'balance' => Auth::user()->balance - $pbid->amount,
            ]);

            $balance_logs = UserBalanceLog::create([
                'user_id'     => Auth::user()->id,
                'order_id'    => $cek->id,
                'type'        => 'Minus',
                'action'      => 'Project',
                'amount'      => $pbid->amount,
                'description' => 'Membuat Project #' . $cek->id . '.',
            ]);


            ProjectBid::where("project_id", $pbid->project_id)->update(["status" => "Approve"]);
            $pbid->update([
                "status" => "Approve"
            ]);
            $user->update([
                "working_project" => $user->working_project + 1,
                "current_project" => $user->current_project + 1
            ]);
            $cek->update([
                "project_status" => "Start",
                "projectbid_id" => $pbid->id
            ]);

            $text = "Selamat (" . $user->username . "), anda mendapatkan tugas (" . $cek->title . "). Dilarang melakukan transaksi di luar sistem TitipTugas.com";
            send_watsapp($user->phone_number, $text);

            return response()->json([
                'status'  => true,
                'message' => 'Update Bid Berhasil'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Update Bid Gagal'
            ]);
        }
    }

    public function editproject(Project $target)
    {
        $cek = Project::where("id", $target->id)->where("user_id", Auth::user()->id)->first();
        if ($cek == false) abort('404');

        $components['breadcrumb'] = (object) [
            'first'  => "My Project",
            'second' => 'Halaman'
        ];
        $components['target'] = $target;
        $components['cat'] = ProjectCategory::get();
        return view('user.project.edit', $components);
    }

    public function postedit(Project $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $cek = Project::where("id", $target->id)->where("user_id", Auth::user()->id)->first();
        if ($cek == false) abort('404');
        if (strlen($request->desc) > 50) {
            $input_data = [
                'user_id'    => Auth::user()->id,
                'title'    => $request->title,
                'deskripsi'    => $request->desc,
                'project_status' => "Pending",
                'reject_note' => "",
                'category_id' => escape_input($request->kategori),
                'estimasi' => escape_input($request->finishday),
                'budget_from' => fixed_amount($request->budgetfrom),
                'budget_to' => fixed_amount($request->budgetto)
            ];
            $cek->update($input_data);

            return response()->json([
                'status'  => true,
                'message' => 'Project Berhasil Di Update'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Deskripsi Harus Lebih Dari 50 Karakter'
            ]);
        }
    }
}
