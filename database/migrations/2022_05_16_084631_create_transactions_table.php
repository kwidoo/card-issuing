<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['capture', 'refund']);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('card_id')->constrained();
            $table->foreignId('authorization_id')->constrained();
            $table->foreignId('merchant_id')->constrained();
            $table->foreignId('dispute_id')->nullable()->constrained();
            $table->string('currency', 3);
            $table->integer('amount');
            $table->integer('atm_fee')->nullable();
            $table->enum('wallet', ['apple_pay', 'google_pay', 'samsung_pay'])->nullable(); //move to config
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
