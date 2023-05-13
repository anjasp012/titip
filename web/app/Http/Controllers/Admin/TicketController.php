<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketReply;
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

class TicketController extends Controller
{
    public function list(TicketDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Tiket',
            'second' => website_config('main')->website_name
        ];
        $components['users'] = Ticket::distinct()->latest('user_id')->get(['user_id']);
        $components['statuses'] = ['Waiting', 'User Reply', 'Replied', 'Closed'];
        $components['created_at'] = Ticket::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        $components['updated_at'] = Ticket::selectRaw('DATE(updated_at) AS updated_at')->distinct()->latest('updated_at')->get();
        return $dataTable->render('admin.ticket.list', $components);
    }
    public function delete(Ticket $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->delete()) {
            $ticket_reply = TicketReply::where('ticket_id', $target->id)->delete();
            return json_encode(['result' => true], JSON_PRETTY_PRINT);
        }
    }
    public function getSend(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $components['users'] = User::where('status', '1')->latest('id')->get();
        return view('admin.ticket.send', $components);
    }
    public function postSend(SendRequest $request)
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
            'user_id'    => escape_input($request->user_id),
            'subject'    => escape_input($request->subject),
            'status'     => 'Waiting',
            'ip_address' => $request->ip()
        ];
        $insert_data = Ticket::create($input_data);
        $insert_reply = TicketReply::create([
            'user_id'   => $input_data['user_id'],
            'ticket_id' => $insert_data->id,
            'message'   => request('message'),
            'sender'    => 'Admin',
            'ip_address' => request()->ip()
        ]);
        if (json_decode($insert_data->user->notification)->ticket == '1') {
            $details = [
                'name'       => $insert_data->user->full_name,
                'id'         => $insert_data->id,
                'subject'    => $insert_data->subject,
                'status'     => 'Direct',
                'ip_address' => $request->ip(),
            ];
            $this->send_email($details, $insert_data->user->email);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Tiket berhasil dikirim, silahkan menunggu balasan Pengguna.'
        ]);
    }
    public function getReply(Ticket $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $components['target'] = $target;
        $read_ticket = $target->update(['is_read_admin' => '1']);
        if ($target->status == 'Closed') {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Tiket sudah ditutup.'
            ]);
        }
        return view('admin.ticket.reply', $components);
    }
    public function postReply(Ticket $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:5|max:300',
        ], [], ['message' => 'Pesan']);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'type'    => 'validation',
                'message' => $validator->errors()->toArray()
            ]);
        }
        $input_data = [
            'user_id'    => $target->user_id,
            'ticket_id'  => $target->id,
            'message'    => $request->message,
            'sender'     => 'Admin',
            'ip_address' => $request->ip()
        ];
        if ($target->user == true and json_decode($target->user->notification)->ticket == '1' and $target->status <> 'Replied') {
            $details = [
                'name'       => $target->user->full_name,
                'id'         => $target->id,
                'subject'    => $target->subject,
                'status'     => 'Replied',
                'ip_address' => $request->ip(),
            ];
            $this->send_email($details, $target->user->email);
        }
        $insert_data = TicketReply::create($input_data);
        $update_data = $target->update([
            'is_read_user' => '0',
            'status'       => 'Replied',
            'updated_at'   => date('Y-m-d H:i:s')
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Tiket berhasil dibalas, silahkan menunggu balasan Pengguna.'
        ]);
    }
    public function close(Ticket $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->status == 'Closed') return response()->json([
            'status'  => false,
            'type'    => 'alert',
            'message' => 'Tiket berstatus Closed.'
        ], 200, [], JSON_PRETTY_PRINT);
        if ($target->user == true and json_decode($target->user->notification)->ticket == '1') {
            $details = [
                'name'       => $target->user->full_name,
                'id'         => $target->id,
                'subject'    => $target->subject,
                'status'     => 'Closed',
                'ip_address' => $request->ip(),
            ];
            $this->send_email($details, $target->user->email);
        }
        $update_data = $target->update([
            'status' => 'Closed',
            'is_read_admin' => '1',
            'is_read_user'  => '1',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json([
            'result'  => true,
        ], 200, [], JSON_PRETTY_PRINT);;
    }
    public function send_email($details = [], $to = '')
    {
        config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
        config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
        config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
        config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
        config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
        config(['mail.from.address' => website_config('smtp')->from]);
        config(['mail.from.name' => website_config('main')->website_name]);
        try {
            Mail::send('user.mail.notification.ticket', $details, function ($message) use ($details, $to) {
                $message
                    ->to($to, $details['name'])
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Informasi Tiket - ' . website_config('main')->website_name . '');
            });
            return true;
        } catch (Exception $message) {
            return true;
        }
    }
}

class SendRequest extends FormRequest
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
            'user_id' => 'required|numeric|exists:users,id',
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ];
    }
    public function attributes()
    {
        return [
            'user_id' => 'Pengguna',
            'subject' => 'Subjek',
            'message' => 'Pesan',
        ];
    }
}
