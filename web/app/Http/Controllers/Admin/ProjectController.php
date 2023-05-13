<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Project;
use App\Models\UserBalanceLog;
use App\Models\PostingLapor;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\ProjectDataTable;
use App\Models\ProjectBid;
use App\Models\ProjectBidMessage;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    public function list(ProjectDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Project',
            'second' => website_config('main')->website_name
        ];
        $components['users'] = Project::distinct()->latest('user_id')->get(['user_id']);
        $components['created_at'] = Project::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        $components['updated_at'] = Project::selectRaw('DATE(updated_at) AS updated_at')->distinct()->latest('updated_at')->get();
        return $dataTable->render('admin.project.list', $components);
    }


    public function pendingproject()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Pending Project',
            'second' => website_config('main')->website_name
        ];
        $components['list'] = Project::where("project_status", "Pending")->get();
        return view('admin.project.pending', $components);
    }


    public function gugatan()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Gugatan Project',
            'second' => website_config('main')->website_name
        ];
        $components['list'] = ProjectBid::where("status", "Gugatan")->get();
        return view('admin.project.gugatan', $components);
    }
    public function gugatandetail(ProjectBid $target)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Detail Gugatan Project',
            'second' => website_config('main')->website_name
        ];
        $components['target'] = $target;
        $components['bidmsg'] = ProjectBidMessage::where("project_id", $target->project_id)->where("user_id", $target->user_id)->orderBy('id', 'desc')->Where("tipe", "1")->get();
        $components['project'] = Project::where("id", $target->project_id)->first();
        $components['tergugat'] = User::where("id", $components['project']->user_id)->first();
        $components['penggugat'] = User::where("id", $target->user_id)->first();
        return view('admin.project.gugatan_detail', $components);
    }


    public function detail(Project $target)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Project Detail',
            'second' => website_config('main')->website_name
        ];
        $components['target'] = $target;
        return view('admin.project.detail', $components);
    }

    public function reject(Request $request)
    {

        if ($request->ajax() == false) abort('404');
        //if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));

        $project = Project::where('id', $request->id)->first();
        if ($project == false) abort('404');
        $project->update([
            "project_status" => "Reject",
            "reject_note" => $request->note
        ]);
        $text = "Mohon maaf (" . $project->user->username . ") , tugas anda belum dapat kami publish, karena alasan " . $request->note;
        send_watsapp($project->user->phone_number, $text);

        return json_encode(['result' => true], JSON_PRETTY_PRINT);
    }

    public function approveproject(Request $request)
    {

        if ($request->ajax() == false) abort('404');
        //if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));

        $project = Project::where('id', $request->id)->first();
        if ($project == false) abort('404');
        $project->update([
            "project_status" => "Active"
        ]);
        $text = "Selamat (" . $project->user->username . ") , tugas anda telah dipublish. Silahkan menunggu user lain untuk memberikan penawaran.";
        send_watsapp($project->user->phone_number, $text);

        return json_encode(['result' => true], JSON_PRETTY_PRINT);
    }

    public function cancelproject(ProjectBid $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target == false) abort('404');
        $cek = Project::where("id", $target->project_id)->first();

        if ($cek) {
            $target->update([
                "status" => "Cancel"
            ]);
            $cek->update([
                "project_status" => "Cancel"
            ]);

            $ownser = User::find($cek->user_id);
            $ownser->update([
                'balance' => $ownser->balance + $target->amount,
            ]);

            $balance_logs = UserBalanceLog::create([
                'user_id'     => $cek->user_id,
                'order_id'    => $cek->id,
                'type'        => 'Plus',
                'action'      => 'Project',
                'amount'      => $target->amount,
                'description' => 'Refund Project #' . $cek->id . '.',
            ]);

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

    public function finishproject(ProjectBid $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $pbid = ProjectBid::where("id", $target->id)->first();
        $cek = Project::where("id", $pbid->project_id)->where("user_id", Auth::user()->id)->first();
        $user = User::where("id", $pbid->user_id)->first();
        if ($cek) {
            $pbid->update([
                "status" => "Ended"
            ]);

            $cek->update([
                "project_status" => "Ended"
            ]);
            /*
            $user->update([
                "current_project" => $user->current_project - 1,
                "balance" => $user->balance + $pbid->amount
            ]);
            */
            $bidder = User::find($pbid->user_id);
            $bidder->update([
                'balance' => $bidder->balance + $target->amount,
            ]);


            $balance_logs = UserBalanceLog::create([
                'user_id'     => $user->id,
                'order_id'    => $cek->id,
                'type'        => 'Plus',
                'action'      => 'Project',
                'amount'      => $pbid->amount,
                'description' => 'Fee Project #' . $cek->id . '.',
            ]);

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

    public function bermasalah()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Posting Bermasalah',
            'second' => website_config('main')->website_name
        ];
        $components['list'] = PostingLapor::where("status", "0")->get();
        return view('admin.posting.bermasalah', $components);
    }

    public function getForm(ProjectCategory $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        $components['levels'] = ['Member', 'Moderator'];
        return view('admin.project.form', $components);
    }

    public function postForm(ProjectCategory $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target->id <> null) {
            $update_data = $target->update([
                "name" => $request->name

            ]);
            return response()->json([
                'status'  => true,
                'message' => 'PostCategory berhasil diperbarui.'
            ]);
        } else {
            $input_data = [
                'name'      => escape_input($request->name)
            ];
            $insert_data = ProjectCategory::create($input_data);
            return response()->json([
                'status'  => true,
                'message' => 'PostCategory berhasil ditambahkan.'
            ]);
        }
    }
    function kategori()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Kategori Project',
            'second' => website_config('main')->website_name
        ];
        $components['category'] = ProjectCategory::get();
        return view('admin.project.kategori', $components);
    }
}
