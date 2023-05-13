<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Deposit;
use App\Models\Product;
use App\Models\Service;
use App\Models\OrderBonus;
use App\Models\BankAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\BankMutation;
use App\Models\DepositMethod;
use App\Models\WebsiteConfig;
use App\Models\UserBalanceLog;
use Illuminate\Support\Carbon;
use App\Models\ProductCategory;
use App\Models\ProductProvider;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ProductSubCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CronjobController extends Controller
{
    public function getdeposit()
    {
        $dd =  Deposit::where('status', 'Pending')->whereRaw((Deposit::raw('DATE(created_at) >= NOW() - INTERVAL 1 DAY')))->first();
        if ($dd) {
            echo $dd->update([
                "status" => "Canceled"
            ]);
        }
    }
}
