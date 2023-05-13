<?php

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\WebsiteConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

function create_api_key()
{
	for ($i = 0; $i < 100; $i++) {
		$random_string = Str::random(25);
		if (User::where('api_key', $random_string) == true) continue;
		break;
	}
	return $random_string;
}

function escape_input($i = '')
{
	return htmlspecialchars(strip_tags($i));
}

function website_config($i = '')
{
	$check_data = WebsiteConfig::where('key', $i)->first();
	if ($check_data == false) return false;
	return json_decode($check_data->value);
}

function status($status = '')
{
	if ($status == 'Pending') {
		return '<span class="badge bg-warning">PENDING</span>';
	} elseif ($status == 'Waiting') {
		return '<span class="badge bg-warning">WAITING</span>';
	} elseif ($status == 'Processing') {
		return '<span class="badge bg-primary">PROCESSING</span>';
	} elseif ($status == 'Success') {
		return '<span class="badge bg-success">SUCCESS</span>';
	} elseif ($status == 'Replied') {
		return '<span class="badge bg-success">REPLIED</span>';
	} elseif ($status == 'Admin Reply') {
		return '<span class="badge bg-success">ADMIN REPLY</span>';
	} elseif ($status == 'User Reply') {
		return '<span class="badge bg-primary">USER REPLY</span>';
	} elseif ($status == 'Error') {
		return '<span class="badge bg-danger">ERROR</span>';
	} elseif ($status == 'Partial') {
		return '<span class="badge bg-danger">PARTIAL</span>';
	} elseif ($status == 'Closed') {
		return '<span class="badge bg-danger">CLOSED</span>';
	} elseif ($status == 'Canceled') {
		return '<span class="badge bg-danger">CANCELED</span>';
	} elseif ($status == '1') {
		return '<span class="badge bg-success">AKTIF</span>';
	} elseif ($status == '0') {
		return '<span class="badge bg-danger">NONAKTIF</span>';
	} else {
		return '<span class="badge bg-info">ERROR</span>';
	}
}
function rupiah($angka)
{

	$hasil_rupiah = number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}

function fixed_amount($i = '')
{
	if (preg_match("/# /i", $i)) {
		$i = str_replace('.', '', $i);
		$i = str_replace('# ', '', $i);
		if (is_numeric($i) == false) return false;
		if ($i < 0) return false;
		return $i;
	}
	if (preg_match("/Rp /i", $i)) {
		$i = str_replace('.', '', $i);
		$i = str_replace('Rp ', '', $i);
		if (is_numeric($i) == false) return false;
		if ($i < 0) return false;
		return $i;
	}
	if (is_numeric($i) == false) return false;
	if ($i < 0) return false;
	return $i;
}

function category($category = '')
{
	if ($category == 'Info') {
		return '<span class="badge badge-info badge-sm">INFO</span>';
	} elseif ($category == 'Maintenance') {
		return '<span class="badge badge-danger badge-sm">MAINTENANCE</span>';
	} elseif ($category == 'Update') {
		return '<span class="badge badge-primary badge-sm">UPDATE</span>';
	} elseif ($category == 'Product') {
		return '<span class="badge badge-success badge-sm">PRODUK</span>';
	} elseif ($category == 'Service') {
		return '<span class="badge badge-success badge-sm">LAYANAN</span>';
	} elseif ($category == 'Other') {
		return '<span class="badge badge-warning badge-sm">OTHER</span>';
	} elseif ($category == 'Cancel') {
		return '<span class="badge badge-danger badge-sm">Cancel</span>';
	} elseif ($category == 'Active') {
		return '<span class="badge badge-info badge-sm">Active</span>';
	} else {
		return '<span class="badge badge-danger badge-sm">ERROR</span>';
	}
}

function getstatus($category = '')
{
	if ($category == 'Info') {
		return '<span class="badge bg-info badge-sm text-white">INFO</span>';
	} elseif ($category == 'Maintenance') {
		return '<span class="badge bg-danger badge-sm text-white">MAINTENANCE</span>';
	} elseif ($category == 'Update') {
		return '<span class="badge bg-primary badge-sm text-white">UPDATE</span>';
	} elseif ($category == 'Product') {
		return '<span class="badge bg-success badge-sm text-white">PRODUK</span>';
	} elseif ($category == 'Service') {
		return '<span class="badge bg-success badge-sm text-white">LAYANAN</span>';
	} elseif ($category == 'Other') {
		return '<span class="badge bg-warning badge-sm text-white">OTHER</span>';
	} elseif ($category == 'Pending') {
		return '<span class="badge bg-warning badge-sm text-white">Pending</span>';
	} elseif ($category == 'Cancel') {
		return '<span class="badge bg-danger badge-sm text-white">Cancel</span>';
	} elseif ($category == 'Reject') {
		return '<span class="badge bg-danger badge-sm text-white">Reject</span>';
	} elseif ($category == 'Active') {
		return '<span class="badge bg-info badge-sm text-white">Active</span>';
	} elseif ($category == 'Ended') {
		return '<span class="badge bg-success badge-sm text-white">Ended</span>';
	} elseif ($category == 'Approve') {
		return '<span class="badge bg-success badge-sm text-white">Approve</span>';
	} elseif ($category == 'Start') {
		return '<span class="badge bg-success badge-sm text-white">Start</span>';
	} elseif ($category == 'Gugatan') {
		return '<span class="badge bg-danger text-white">Gugatan</span>';
	} elseif ($category == 'Finish') {
		return '<span class="badge bg-info badge-sm text-white">Finish</span>';
	} else {
		return '<span class="badge bg-danger badge-sm text-white">' . $category . '</span>';
	}
}

function diff_date($date_a, $date_b)
{
	$date_a = date_create($date_a);
	$date_b = date_create($date_b);
	$diff = date_diff($date_a, $date_b);
	return $diff->format("%R%a");
}

function formatTgl($tgl)
{
	$newDate = date("d/m/Y H:i", strtotime($tgl));
	return $newDate;
}

function post_curl($end_point, $header = null, $post)
{
	$_post = array();
	if (is_array($post)) {
		foreach ($post as $name => $value) {
			$_post[] = $name . '=' . urlencode($value);
		}
	}
	$ch = curl_init($end_point);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if (is_array($post)) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
	}
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$result = curl_exec($ch);
	if (curl_errno($ch) != 0 && empty($result)) {
		$result = false;
	}
	curl_close($ch);
	return $result;
}

function time_elapsed_string($datetime, $full = false)
{
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'Tahun',
		'm' => 'Bulan',
		'w' => 'Minggu',
		'd' => 'Hari',
		'h' => 'Jam',
		'i' => 'Menit',
		's' => 'Detik',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' Yang Lalu' : 'Baru Saja';
}
function getLevel($number)
{
	if ($number <= 5000) {
		return "PLAYGROUP";
	} elseif ($number > 5000 && $number <= 10000) {
		return "TK";
	} elseif ($number > 10000 && $number <= 15000) {
		return "SD";
	} elseif ($number > 15000 && $number <= 20000) {
		return "SMP";
	} elseif ($number > 20000 && $number <= 30000) {
		return "SMA";
	} elseif ($number > 30000 && $number <= 50000) {
		return "SARJANA";
	} elseif ($number > 50000 && $number <= 80000) {
		return "MASTER";
	} elseif ($number > 80000 && $number <= 120000) {
		return "DOKTOR";
	} else {
		return "PROFESOR";
	}
}

function send_watsapp($number, $text)
{

	$curl = curl_init();
	$token = 'HRnII7lC66PPjg7KKdWztBsZpBpOrOe77JDi44Ia4snj56A6dy';
	$message = $text;
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://nusagateway.com/api/send-message.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('token' =>  $token, 'phone' => $number, 'message' => $message),
	));

	$response = curl_exec($curl);
	curl_close($curl);
	//echo $response;
}

function cleatags($string)
{
	return str_replace("&nbsp;", " ", Str::words(strip_tags($string), '25'));
}
