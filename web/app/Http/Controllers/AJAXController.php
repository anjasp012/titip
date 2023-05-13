<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Product;
use App\Models\Operator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DepositMethod;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Support\Facades\Auth;
use Propaganistas\LaravelPhone\PhoneNumber;

class AJAXController extends Controller {
    public function __construct(Request $request) {
		if ($request->ajax() == false) abort('404');
    }
    public function create_api_key() {
        for ($i = 0; $i < 100; $i++) {
            $random_string = Str::random(25);
            if (User::where('api_key', $random_string) == true) continue;
            break;
        }
        return $random_string;
    }
    public function deposit_get_balance(Request $request) {
        $input_data = [
            'deposit_method_id' => escape_input($request->deposit_method_id),
        ];
        $check_data = DepositMethod::find($input_data['deposit_method_id']);
        if ($check_data == false) {
            $result = [
                'data' => [
                    'rate' => '0',
                    'min'  => '0',
                ]
            ];
        } else {
			$result = [
                'data' => [
                    'rate' => $check_data->rate,
                    'min'  => '<small class="form-text text-default">Minimal: Rp '.number_format($check_data->min,0,',','.').'</small>'
                ]
            ];
        }
        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
    public function category(Request $request) {
        $input_data = [
            'category_id' => escape_input($request->category_id),
        ];
        $check_data = ProductSubCategory::where('category_id', $input_data['category_id'])->where('status', '1')->orderBy('name', 'asc')->get();
        if ($check_data == false) {
            $result = [
                'data' => '<option value="">Pilih Kategori...</option>'
            ];
        } else {
            $options = '<option value="">Pilih...</option>';
			foreach ($check_data as $key => $value) {
				$options .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
			}
			$result = [
                'data' => $options
            ];
        }
        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
    public function product_list(Request $request) {
        $input_data = [
            'category' => escape_input($request->category),
            'phone_number' => escape_input($request->phone_number),
        ];
        try {
            $check_category = ProductCategory::where('slug', $input_data['category'])->first();
            if ($check_category == false) return json_encode(['result' => false, 'message' => 'Kategori tidak ditemukan.'], JSON_PRETTY_PRINT);
            if ($input_data['category'] <> 'token-pln') {
                $phone = PhoneNumber::make($input_data['phone_number'], 'ID')->isOfCountry('ID');
                $phone = PhoneNumber::make($input_data['phone_number'], 'ID')->formatForMobileDialingInCountry('ID');
                $cut_phone = substr($phone, 0, 4);
                $check_operator = Operator::where('code', $cut_phone)->first();
                if ($check_operator == false) return json_encode(['result' => false, 'message' => 'No. Handphone tidak valid'], JSON_PRETTY_PRINT);
            }
            if ($input_data['category'] == 'token-pln' OR $input_data['category'] == 'saldo-e-money' OR $input_data['category'] == 'voucher-game') {
                $check_sub_category = ProductSubCategory::where('category_id', $check_category->id)->first();
            } else {
                $check_sub_category = ProductSubCategory::where('category_id', $check_category->id)->where('name', $check_operator->name)->first();
            }
            if ($check_sub_category == false) return json_encode(['result' => false, 'message' => 'Produk tidak ditemukan.'], JSON_PRETTY_PRINT);
            // return json_encode(['result' => true, 'message' => 'Produk tersedia.'], JSON_PRETTY_PRINT);
            if (request('sub_category') == true AND request('sub_category') <> null) {
                $check_sub_category->id = request('sub_category');
            }
            $check_product = Product::where([
                ['sub_category_id',  $check_sub_category->id]
            ])->where('status', '1')->get();
            if ($check_product == false) {
                $result = [
                    'data' => '<a href="javascript:void(0)"><div class="alert alert-danger">Tidak ada produk yang tersedia.</div></a>'
                ];
            } else {
                foreach ($check_product as $key => $value) {
                    if (Auth::user()->level == 'Agen') {
                        $price = $value['agen_price'];
                        $info = "";
                    } else {
                        $price = $value['reseller_price'];
                        $info = "- (<b>Harga Reseller</b>)";
                    }
                    if ($input_data['category'] == 'token-pln') $phone = $input_data['phone_number'];
                    if ($input_data['category'] <> 'token-pln') $type = '';
                    if ($input_data['category'] == 'token-pln') $type = 'pln';
                    $options[] = "<a href=\"javascript:;\" onclick=\"purchaseProduct('$value->name', '$phone', 'Rp ".number_format($price,0,',','.')."', '".url('order/product/'.$value->id.'')."', '$type')\">
					<div class=\"alert alert-info\">
						<b>$value->name</b> 
						<div class=\"float-right\">
							<b>Rp ".number_format($price,0,',','.')." ".$info."</b>
						</div>
					</div>
					</a>";
                }
            }
            exit(json_encode(['result' => true, 'data' => $options], JSON_PRETTY_PRINT));
        } catch (Exception $error) {
            exit(json_encode(['result' => false, 'message' => 'No. Handphone tidak valid.'], JSON_PRETTY_PRINT));
        }
    }
}
