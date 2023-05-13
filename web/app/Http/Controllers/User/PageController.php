<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\WebsiteInformation;
use App\Http\Controllers\Controller;
use App\DataTables\User\Page\InformationDataTable;

class PageController extends Controller
{
    public function site(WebsitePage $target)
    {
        $components['breadcrumb'] = (object) [
            'first'  => $target->title,
            'second' => 'Halaman'
        ];
        $components['target'] = $target;
        return view('user.page.site', $components);
    }
    public function information(InformationDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Informasi',
            'second' => 'Halaman'
        ];
        $components['categories'] = ['Info', 'Maintenance', 'Update', 'Product', 'Service', 'Other'];
        $components['created_at'] = WebsiteInformation::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('user.page.information.list', $components);
    }

    public function detailinformation(WebsiteInformation $target)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Informasi',
            'second' => 'Halaman'
        ];
        $components['target'] = $target;
        return view('user.page.information.infodetail', $components);
    }
    public function api_doc()
    {
        $components['breadcrumb'] = (object) [
            'first'  => 'Dokumentasi API',
            'second' => 'Halaman'
        ];
        return view('user.page.api_doc', $components);
    }
    public function hof()
    {
        $components = [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-t')
        ];
        $components['breadcrumb'] = (object) [
            'first'  => 'Top Terbaik',
            'second' => 'Halaman'
        ];
        $components['orders'] = User::whereBetween('orders.created_at', [$components['start_date'], $components['end_date']])
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->latest('amount')
            ->whereIn('orders.status', ['Pending', 'Processing', 'Success'])
            ->groupBy('users.full_name', 'users.id')
            ->limit('10')
            ->get(['users.full_name', 'users.id', User::raw('SUM(orders.price) AS amount'), User::raw('COUNT(orders.id) AS total')]);
        $components['deposits'] = User::whereBetween('deposits.created_at', [$components['start_date'], $components['end_date']])
            ->join('deposits', 'users.id', '=', 'deposits.user_id')
            ->latest('amount')
            ->whereIn('deposits.status', ['Success'])
            ->groupBy('users.full_name', 'users.id')
            ->limit('10')
            ->get(['users.full_name', 'users.id', User::raw('SUM(deposits.balance) AS amount'), User::raw('COUNT(deposits.id) AS total')]);
        $components['services'] = Order::whereBetween('orders.created_at', [$components['start_date'], $components['end_date']])
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->latest('amount')
            ->whereIn('orders.status', ['Pending', 'Processing', 'Success'])
            ->groupBy('services.name', 'services.id')
            ->limit('10')
            ->get(['services.name', 'services.id', User::raw('SUM(orders.price) AS amount'), User::raw('COUNT(orders.id) AS total')]);
        return view('user.page.hof', $components);
    }
    public function product_list(Request $request)
    {
        $config['category_id'] = '0';
        if (request('category_id') <> '' and request('category_id') <> '') {
            if (is_numeric(request('category_id')) == true) {
                $config['category_id'] = request('category_id');
            }
        }
        $components['table'] = Product::join('product_categories', 'products.category_id', '=', 'product_categories.id')->join('product_sub_categories', 'products.sub_category_id', '=', 'product_sub_categories.id')->select('products.*', 'product_categories.name AS category', 'product_sub_categories.name AS sub_category')->when(request(), function ($query) use ($request) {
            if (request('category_id') <> '' and request('category_id') <> '') {
                if (is_numeric(request('category_id')) == true) {
                    $query->where('products.category_id', request('category_id'));
                }
            }
            if (request('sub_category_id') <> '' and request('sub_category_id') <> '') {
                if (is_numeric(request('sub_category_id')) == true) {
                    $query->where('products.sub_category_id', request('sub_category_id'));
                }
            }
            if (request('search_value') <> '' and request('search_value') <> '') {
                $query->where('products.name', 'like', '%' . htmlspecialchars(strip_tags(request('search_value'))) . '%');
            }
        })->get();
        $components = [
            'categories'       => ProductCategory::orderBy('id', 'desc')->get(),
            'sub_categories'  => ProductSubCategory::where('category_id', $config['category_id'])->get(),
            'table'           => $components['table'],
        ];
        $components['breadcrumb'] = (object) [
            'first'  => 'Daftar Produk',
            'second' => 'Halaman'
        ];
        return view('user.page.product.list', $components);
    }
    public function product_detail(Product $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        return view('user.page.product.detail', compact('target'));
    }
}
