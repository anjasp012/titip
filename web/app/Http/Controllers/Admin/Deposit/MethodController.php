<?php

namespace App\Http\Controllers\Admin\Deposit;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\DepositMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\DataTables\Admin\Deposit\MethodDataTable;
use Illuminate\Http\Exceptions\HttpResponseException;

class MethodController extends Controller
{
    public function list(MethodDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Metode',
            'second' => 'Deposit'
        ];
        $components['payments'] = ['Transfer', 'E-Money', 'Virtual Account', 'Retail'];
        $components['types'] = ['Auto', 'Manual'];
        $components['statuses'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        return $dataTable->render('admin.deposit.method.list', $components);
    }
    public function getForm(DepositMethod $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        $components['payments'] = ['Transfer', 'E-Money', 'Virtual Account', 'Retail'];
        $components['types'] = ['Auto', 'Manual'];
        return view('admin.deposit.method.form', $components);
    }
    public function postForm(DepositMethod $target, PostRequest $request)
    {
        if ($request->ajax() == false) abort('404');
        /*if (Auth::guard('admin')->user()->level == 'Admin') {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }*/
        $input_data = [
            'payment' => escape_input($request->payment),
            'type'    => escape_input($request->type),
            'name'    => escape_input($request->name),
            'rate'    => $request->rate,
            'min'     => fixed_amount($request->min),
            'api_id'    => escape_input($request->api_id),
            'api_url'    => escape_input($request->api_url),
            'api_private'    => escape_input($request->api_private),
            'api_public'    => escape_input($request->api_public),
            'merchant'    => escape_input($request->merchant),
            'merchant_code'    => escape_input($request->merchant_code),
            'rek'    => escape_input($request->rek),
            'rek_name'    => escape_input($request->rek_name),
            'note'    => $request->note,
        ];
        if ($target->id <> null) {
            $check_data = DepositMethod::where([['name', $input_data['name']]])->first();
            if ($input_data['name'] <> $target['name'] and $check_data) {
                if ($input_data['name'] <> $target['name'] and $check_data) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required|unique:deposit_methods,name|max:40',
                    ], [], ['name' => 'Nama']);
                    if ($validator->fails()) {
                        return response()->json([
                            'status'  => false,
                            'type'    => 'validation',
                            'message' => $validator->errors()->toArray()
                        ]);
                    }
                }
            }
            $update_data = $target->update($input_data);
            return response()->json([
                'status'  => true,
                'message' => 'Metode berhasil diperbarui.'
            ]);
        } else {
            $insert_data = DepositMethod::create($input_data);
            return response()->json([
                'status'  => true,
                'message' => 'Metode berhasil ditambahkan.'
            ]);
        }
    }
    public function status(DepositMethod $target, $status, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if (Arr::exists(['0', '1'], $status) == false) return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        $update_data = $target->update(['status' => $status]);
        if ($update_data == true) {
            return (json_encode([
                'result'  => true,
                'message' => 'Status Metode <b>' . $target->name . '</b> berhasil diperbarui.'
            ], JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        }
    }
    public function delete(DepositMethod $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->delete()) return json_encode(['result' => true], JSON_PRETTY_PRINT);
    }
    public function detail(DepositMethod $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        return view('admin.deposit.method.detail', compact('target'));
    }
}

class PostRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'min' => $this->min <> '' ? fixed_amount($this->min) : '',
        ]);
    }
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
        if (request()->segment(5) == null) {
            return [
                'payment' => 'required|in:Transfer,E-Money,Virtual Account,Retail',
                'type'    => 'required|in:Auto,Manual',
                'name'    => 'required|unique:deposit_methods,name',
                'rate'    => 'required|numeric|min:0',
                'min'     => 'required|numeric|integer|min:0',
                'note'    => 'required|string',
            ];
        }
        return [
            'payment' => 'required|in:Transfer,E-Money,Virtual Account,Retail',
            'type'    => 'required|in:Auto,Manual',
            'name'    => 'required',
            'rate'    => 'required|numeric|min:0',
            'min'     => 'required|numeric|integer|min:0',
            'note'    => 'required|string',
        ];
    }
    public function attributes()
    {
        return [
            'payment' => 'Pembayaran',
            'type'    => 'Tipe',
            'name'    => 'Nama',
            'rate'    => 'Rate',
            'min'     => 'Minimal',
            'note'    => 'Catatan',
        ];
    }
}
