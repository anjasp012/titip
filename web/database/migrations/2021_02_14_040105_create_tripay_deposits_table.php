<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripayDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tripay_deposits', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id');
            $table->string('deposit_id');
            $table->string('payment_name');
            $table->double('amount');
            $table->enum('status', ['PAID', 'UNPAID', 'CANCELED', 'OTHER']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tripay_deposits');
    }
}
