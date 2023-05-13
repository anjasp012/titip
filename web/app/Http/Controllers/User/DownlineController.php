<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\OrderBonus;
use App\Models\Penarikan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\User\DownlineDataTable;

class DownlineController extends Controller
{
    public function summary(DownlineDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Ringkasan',
            'second' => 'Downline'
        ];
        $components['created_at'] = User::where('upline', Auth::user()->username)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        $components['referral_code_used'] = User::where('upline', Auth::user()->id)->get();
        $components['bonus_received']     = OrderBonus::where('user_id', Auth::user()->id)->where('is_sent', '1')->get();
        return $dataTable->render('user.downline.summary', $components);
    }

    public function wdbonus(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $user = User::where("id", Auth::user()->id)->first();
        $nominal = fixed_amount($request->nominal);
        if ($user->bonus_rp < $nominal) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Maaf Saldo Anda tidak mencukupi.'
            ]);
        }
        if ($nominal < 200000) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Maaf Penarikan Saldo Minimum Rp.200.000'
            ]);
        }
        $user->update([
            "bonus_rp" => $user->bonus_rp - $nominal
        ]);
        $nominal2 =  $nominal - website_config('bonus_point')->adminwd;
        $input_data = [
            'user_id'           => Auth::user()->id,
            'amount'            => $nominal2,
            'bank'              => $request->bank,
            'rekening'           => $request->rekening,
            'jenis'            => "Bonus",
            'nama'           => $request->nama,
        ];
        $insert_data = Penarikan::create($input_data);


        $text = "Permintaan penarikan anda sudah masuk ke dalam sistem, silahkan tunggu paling lama 2x24 jam untuk ditransfer ke rekening anda";
        send_watsapp($user->phone_number, $text);

        if ($insert_data == true) {
            return response()->json([
                'status'  => true,
                'type'    => 'alert',
                'message' => 'Penarikan berhasil. penarikan akan di proses dalam waktu 1x24 Jam'
            ]);
        }
    }
}
