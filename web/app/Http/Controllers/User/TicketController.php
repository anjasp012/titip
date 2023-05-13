<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\User\TicketDataTable;
use App\Http\Requests\User\TicketRequest;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller {
    public function list(TicketDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Tiket',
			'second' => 'Tiket'
		];
        $components['statuses'] = ['Waiting', 'User Reply', 'Replied', 'Closed'];
        $components['created_at'] = Ticket::where('user_id', Auth::user()->id)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        $components['updated_at'] = Ticket::where('user_id', Auth::user()->id)->selectRaw('DATE(updated_at) AS updated_at')->distinct()->latest('updated_at')->get();
        return $dataTable->render('user.ticket.list', $components);
    }
    public function getSend(Request $request) {
		if ($request->ajax() == false) abort('404');
        return view('user.ticket.send');
    }
    public function postSend(PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::user()->username == 'demouser') {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'user_id'       => Auth::user()->id,
            'subject'       => escape_input($request->subject),
            'status'        => 'Waiting',
            'is_read_user'  => '1',
            'is_read_admin' => '0',
            'ip_address'    => $request->ip()
        ];
		$check_data = Ticket::where([
            ['user_id', Auth::user()->id],
            ['status', 'Waiting'],
        ])->whereDate('created_at', date('Y-m-d'))->get();
		if ($check_data->count() >= 3) {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Anda masih memiliki '.$check_data->count().' Tiket berstatus Waiting.'
			]);
		} else {
			$insert_data = Ticket::create($input_data);
			if ($insert_data == true) {
                $insert_reply = TicketReply::create([
                    'user_id'    => $input_data['user_id'],
                    'ticket_id'  => $insert_data->id,
                    'message'    => escape_input($request->message),
                    'sender'     => 'User',
                    'ip_address' => $request->ip()
                ]);
                if (json_decode(Auth::user()->notification)->ticket == '1') {
                    $details = [
                        'name'       => Auth::user()->full_name,
                        'id'         => $insert_data->id,
                        'subject'    => $input_data['subject'],
                        'status'     => 'Waiting',
                        'ip_address' => $request->ip(),
                    ];
                    $this->send_email_user($details, Auth::user()->email);
                }
                if (website_config('notification')->email <> '' AND website_config('notification')->ticket == '1') {
                    $details = [
                        'username'   => Auth::user()->username,
                        'full_name'  => Auth::user()->full_name,
                        'id'         => $insert_data->id,
                        'subject'    => $input_data['subject'],
                        'status'     => 'Waiting',
                        'ip_address' => $request->ip(),
                    ];
                    $this->send_email_admin($details, website_config('notification')->email);
                }
                return response()->json([
                    'status'  => true, 
                    'message' => 'Tiket berhasil dikirim, silahkan menunggu balasan Admin.'
                ]);
			} else {
                return response()->json([
                    'status'  => false, 
                    'type'    => 'alert',
                    'message' => 'Terjadi kesalahan.'
                ]);
			}
		}
    }
    public function getReply(Ticket $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        $read_ticket = $target->update(['is_read_user' => '1']);
        if ($target->status == 'Closed') {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Tiket sudah ditutup.'
			]);
        }
        $components = [
            'ticket_replies' => TicketReply::where('user_id', Auth::user()->id)->where('ticket_id', $target->id)->latest('id')->get(),
            'target'         => $target,
        ];
        return view('user.ticket.reply', $components);
    }
    public function postReply(PostRequest $request, Ticket $target) {
		if ($request->ajax() == false) abort('404');
		$input_data = [
            'user_id'    => $target->user_id,
            'ticket_id'  => $target->id,
            'message'    => escape_input($request->message),
            'sender'     => 'User',
            'ip_address' => $request->ip()
        ];
		$insert_data = TicketReply::create($input_data);
		if ($insert_data == true) {
            if (website_config('notification')->email <> '' AND website_config('notification')->ticket == '1' AND $target->status <> 'User Reply') {
                $details = [
                    'username'   => Auth::user()->username,
                    'full_name'  => Auth::user()->full_name,
                    'id'         => $target->id,
                    'subject'    => $target['subject'],
                    'status'     => 'User Reply',
                    'ip_address' => $request->ip(),
                ];
                $this->send_email_admin($details, website_config('notification')->email);
            }
            $update_data = $target->update([
                'is_read_admin' => '0',
                'status'        => 'User Reply',
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            return response()->json([
				'status'  => true, 
                'message' => 'Tiket berhasil dibalas, silahkan menunggu balasan Admin.'
			]);
		} else {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Terjadi kesalahan.'
			]);
		}
    }
    public function send_email_user($details = [], $to = '') {
		config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
		config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
		config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
		config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
		config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
		config(['mail.from.address' => website_config('smtp')->from]);
		config(['mail.from.name' => website_config('main')->website_name]);
		try {
            Mail::send('user.mail.notification.ticket', $details, function($message) use ($details, $to) {
                $message
                 ->to($to, $details['name'])
                 ->from(config('mail.from.address'), config('mail.from.name'))
                 ->subject('Informasi Tiket - '.website_config('main')->website_name.'');
             });
			return true;
		} catch (Exception $message) {
			return true;
		}
    }
    public function send_email_admin($details = [], $to = '') {
		config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
		config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
		config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
		config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
		config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
		config(['mail.from.address' => website_config('smtp')->from]);
		config(['mail.from.name' => website_config('main')->website_name]);
		try {
            Mail::send('admin.mail.notification.ticket', $details, function($message) use ($details, $to) {
                $message
                 ->to($to, 'Admin')
                 ->from(config('mail.from.address'), config('mail.from.name'))
                 ->subject('Informasi Tiket - '.website_config('main')->website_name.'');
             });
			return true;
		} catch (Exception $message) {
			return true;
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
        if (request()->path() == 'ticket/send') {
            return [
                'subject'  => 'required|min:5|max:20',
                'message'  => 'required|min:5',
            ];
        } else {
            return [
                'message'  => 'required|min:5',
            ];
        }
    }
    public function attributes() {
        if (request()->path() == 'ticket/send') {
            return [
                'subject'  => 'Subjek',
                'message'  => 'Pesan',
            ];
        } else {
            return [
                'message'  => 'Pesan',
            ];
        }
    }
}