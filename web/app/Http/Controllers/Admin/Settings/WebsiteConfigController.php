<?php

namespace App\Http\Controllers\Admin\Settings;

use Exception;
use Illuminate\Http\Request;
use App\Models\WebsiteConfig;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class WebsiteConfigController extends Controller
{
	public function __construct()
	{
		$this->image_path = '/public/assets/images/';
		$this->breadcrumb = (object) [
			'first' => 'Konfigurasi Website',
			'second' => 'Pengaturan'
		];
	}
	public function getIndex(Request $request)
	{
		$components['image_path'] = $this->image_path;
		$components['breadcrumb'] = $this->breadcrumb;
		return view('admin.settings.website_configs.index', $components);
	}
	public function postIndex(PostRequest $request)
	{
		if (Auth::guard('admin')->user()->level == 'Admin') {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}
		$input_data = [
			'template' => [
				'number' 	=> is_null($request->website_template) ? '' : $request->website_template,
			],
			'main' => [
				'website_name'    				=> $request->website_name,
				'website_logo'     				=> optional(website_config('main'))->website_logo,
				'website_favicon'     		    => optional(website_config('main'))->website_favicon,
				'about_us'  	   				=> $request->about_us,
				'meta_author'    				=> 'jhonroot',
				'meta_keywords'    				=> $request->meta_keywords,
				'meta_description' 				=> $request->meta_description,
				'is_email_confirmation_enabled' => is_null($request->is_email_confirmation_enabled) ? '' : '1',
				'is_register_enabled' 			=> is_null($request->is_register_enabled) ? '' : '1',
				'is_reset_password_enabled' 	=> is_null($request->is_reset_password_enabled) ? '' : '1',
				'is_website_under_maintenance'  => is_null($request->is_website_under_maintenance) ? '' : '1',
				'is_landing_page_enabled'  	 	=> is_null($request->is_landing_page_enabled) ? '' : '1',
			],
			'socials' => [
				'facebook' 	=> is_null($request->socials_facebook) ? '' : $request->socials_facebook,
				'instagram'	=> is_null($request->socials_instagram) ? '' : $request->socials_instagram,
				'whatsapp' 	=> is_null($request->socials_whatsapp) ? '' : $request->socials_whatsapp,
				'telegram' 	=> is_null($request->socials_telegram) ? '' : $request->socials_telegram,
				'twitter' 	=> is_null($request->socials_twitter) ? '' : $request->socials_twitter,
			],
			'other' => [
				'order_info'  => is_null($request->order_info) ? '' : $request->order_info,
				'deposit_info' => is_null($request->deposit_info) ? '' : $request->deposit_info,
			],
			'smtp' => [
				'auth' 		 => is_null($request->smtp_auth) ? '' : '1',
				'host'		 => is_null($request->smtp_host) ? '' : $request->smtp_host,
				'port'		 => is_null($request->smtp_port) ? '' : $request->smtp_port,
				'from' 		 => is_null($request->smtp_from) ? '' : $request->smtp_from,
				'encryption' => is_null($request->smtp_encryption) ? '' : $request->smtp_encryption,
				'username' 	 => is_null($request->smtp_username) ? '' : $request->smtp_username,
				'password' 	 => is_null($request->smtp_password) ? '' : $request->smtp_password,
			],
			'notification' => [
				'email'   => $request->notification_email,
				'order'   => is_null($request->notification_order) ? '' : '1',
				'deposit' => is_null($request->notification_deposit) ? '' : '1',
				'ticket'  => is_null($request->notification_ticket) ? '' : '1',
			],
			'product_profit' => [
				'agen_price'     => $request->profit_agen_price,
				'reseller_price' => is_null($request->profit_reseller_price) ? '' : '1',
				'order_bonus'    => is_null($request->order_bonus) ? '' : '1',
			],
			'banner' => [
				'value' => is_null($request->website_banner) ? '' : website_config('banner')->value,
			],
		];
		if ($request->website_logo) {
			//$image_name = md5(time().rand()).'.'.$request->website_logo->extension().'';
			$image_name = 'website-logo.' . $request->website_logo->extension() . '';
			$request->website_logo->move(getcwd() . $this->image_path, $image_name);
			$input_data['main']['website_logo'] = url('public/assets/images/' . $image_name . '');
		}
		if ($request->website_favicon) {
			//$image_name = md5(time().rand()).'.'.$request->website_logo->extension().'';
			$image_name = 'website-favicon.' . $request->website_favicon->extension() . '';
			$request->website_favicon->move(getcwd() . $this->image_path, $image_name);
			$input_data['main']['website_favicon'] = url('public/assets/images/' . $image_name . '');
		}
		if ($request->website_banner) {
			//$image_name = md5(time().rand()).'.'.$request->website_logo->extension().'';
			$image_name = 'website-banner.' . $request->website_banner->extension() . '';
			$request->website_banner->move(getcwd() . $this->image_path, $image_name);
			$input_data['banner']['value'] = url('public/assets/images/' . $image_name . '');
		}
		if ($request->smtp_host == '' || $request->smtp_port == '' || $request->smtp_from == '' || $request->smtp_encryption == '') {
			$input_data['main']['is_email_confirmation_enabled'] = '';
		}
		try {
			$update_data = WebsiteConfig::where('key', 'template')->update(['value' => json_encode($input_data['template'])]);
			$update_data = WebsiteConfig::where('key', 'main')->update(['value' => json_encode($input_data['main'])]);
			$update_data = WebsiteConfig::where('key', 'socials')->update(['value' => json_encode($input_data['socials'])]);
			$update_data = WebsiteConfig::where('key', 'other')->update(['value' => json_encode($input_data['other'])]);
			$update_data = WebsiteConfig::where('key', 'smtp')->update(['value' => json_encode($input_data['smtp'])]);
			$update_data = WebsiteConfig::where('key', 'notification')->update(['value' => json_encode($input_data['notification'])]);
			$update_data = WebsiteConfig::where('key', 'product_profit')->update(['value' => json_encode($input_data['product_profit'])]);
			$update_data = WebsiteConfig::where('key', 'banner')->update(['value' => json_encode($input_data['banner'])]);
			return redirect()->back()->with('result', [
				'alert'   => 'success',
				'title'   => 'Berhasil',
				'message' => 'Konfigurasi Website berhasil diperbarui.'
			]);
		} catch (Exception $exception) {
			return redirect()->back()->withInput()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => $exception->getMessage()
			]);
			return dd($exception->getMessage());
		}
	}
	public function delete_logo()
	{
		if (Auth::guard('admin')->user()->level == 'Admin') {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}
		$check_data = WebsiteConfig::where('key', 'main')->first();
		foreach (json_decode($check_data->value, true) as $key => $item) {
			$array[$key] = $item;
			if ($key == 'website_logo') {
				$array[$key] = '';
			}
		}
		$check_data->update([
			'value' => json_encode($array)
		]);
		return redirect('admin/settings/website_configs')->with('result', [
			'alert'   => 'success',
			'title'   => 'Berhasil',
			'message' => 'Logo berhasil dihapus.'
		]);
	}
	public function delete_banner()
	{
		if (Auth::guard('admin')->user()->level == 'Admin') {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}
		$check_data = WebsiteConfig::where('key', 'banner')->first();
		$check_data->update([
			'value' => json_encode(['value' => ''])
		]);
		return redirect('admin/settings/website_configs')->with('result', [
			'alert'   => 'success',
			'title'   => 'Berhasil',
			'message' => 'Banner berhasil dihapus.'
		]);
	}
	public function delete_favicon()
	{
		if (Auth::guard('admin')->user()->level == 'Admin') {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}
		$check_data = WebsiteConfig::where('key', 'main')->first();
		foreach (json_decode($check_data->value, true) as $key => $item) {
			$array[$key] = $item;
			if ($key == 'website_favicon') {
				$array[$key] = '';
			}
		}
		$check_data->update([
			'value' => json_encode($array)
		]);
		return redirect('admin/settings/website_configs')->with('result', [
			'alert'   => 'success',
			'title'   => 'Berhasil',
			'message' => 'Favicon berhasil dihapus.'
		]);
	}
	public function test_email()
	{
		if (Auth::guard('admin')->user()->level == 'Admin') {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}
		if (website_config('smtp')->host == '' || website_config('smtp')->port == '' || website_config('smtp')->from == '' || website_config('smtp')->encryption == '') {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => 'Mohon untuk bidang melengkapi SMTP.'
			]);
		}
		config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
		config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
		config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
		config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
		config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
		config(['mail.from.address' => website_config('smtp')->from]);
		config(['mail.from.name' => website_config('main')->website_name]);
		try {
			Mail::raw(website_config('main')->website_name, function ($message) {
				$message
					->to(website_config('smtp')->from)
					->from(config('mail.from.address'), config('mail.from.name'))
					->subject('Tes SMTP Email');
			});
			return redirect()->back()->with('result', [
				'alert'   => 'success',
				'title'   => 'Berhasil',
				'message' => 'Silahkan periksa Email: ' . website_config('smtp')->from . '.'
			]);
		} catch (Exception $message) {
			return redirect()->back()->with('result', [
				'alert'   => 'danger',
				'title'   => 'Gagal',
				'message' => $message->getMessage()
			]);
		}
	}

	public function poinwd()
	{
		$components['image_path'] = $this->image_path;
		$components['breadcrumb'] = $this->breadcrumb;
		return view('admin.settings.website_configs.pointwd', $components);
	}
	function postpoinwd(Request $request)
	{
		if ($request->ajax() == false) abort('404');
		$check_data = WebsiteConfig::where('key', 'bonus_point')->first();
		$dataupdate =  '{"jawab":"' . $request->jawab . '","upline":"' . $request->upline . '","adminwd":"' . $request->adminwd . '","minwd":"' . $request->minwd . '"}';
		$check_data->update([
			"value" => $dataupdate
		]);
		if ($check_data == true) {
			return response()->json([
				'status'  => true,
				'type'    => 'alert',
				'message' => 'Update Data Berhasil Dilakukan'
			]);
		}
	}
}

class PostRequest extends FormRequest
{
	protected function prepareForValidation()
	{
		$this->merge([
			'profit_agen_price' => $this->profit_agen_price <> '' ? fixed_amount($this->profit_agen_price) : '',
			'profit_reseller_price' => $this->profit_reseller_price <> '' ? fixed_amount($this->profit_reseller_price) : '',
			'order_bonus' => $this->order_bonus <> '' ? fixed_amount($this->order_bonus) : '',
		]);
	}
	public function rules(Request $request)
	{
		return [
			'website_template' 	  	=> 'required|in:first-template,second-template,third-template,fourth-template,custom-template',
			'website_name'  		=> 'required|string',
			'website_logo' 			=> 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'website_logo' 			=> 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			/*'website_banner' 		=> 'required|image|mimes:jpeg,png,jpg|max:2048',*/
			'about_us'  			=> 'required',
			'meta_keywords'  	 	=> 'required',
			'meta_description'	 	=> 'required',
			'notification_email' 	=> $request->notification_email <> null ? 'email' : '',
			'profit_agen_price'     => 'required|numeric|integer|min:0',
			'profit_reseller_price' => 'required|numeric|integer|min:0',
			'order_bonus'           => 'required|numeric|integer|min:0',
		];
	}
	public function attributes()
	{
		return [
			'website_template'  	=> 'Tampilan Website',
			'website_name'  	 	=> 'Nama Website',
			'website_logo'   	 	=> 'Logo Website',
			'website_favicon'    	=> 'Logo Favicon',
			/*'website_banner' 		=> 'Website Banner',*/
			'about_us'  		 	=> 'Tentang Kami',
			'meta_keywords'  	 	=> 'Meta Keywords',
			'meta_description'	 	=> 'Meta Description',
			'notification_email'    => 'Email Penerima Notifikasi',
			'profit_agen_price'     => 'Keuntungan Harga Agen',
			'profit_reseller_price' => 'Keuntungan Harga Reseller',
			'order_bonus'           => 'Bonus Pesanan (Poin)',
		];
	}
}
