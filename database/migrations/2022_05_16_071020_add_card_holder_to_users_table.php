<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardHolderToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var string */
        $modelName = config('card-issuing.cardholder_model', 'App\User');
        /** @var string */
        $tableName = (new $modelName)->getTable();
        Schema::table($tableName, function (Blueprint $table) {
            /** @var string */
            $columnName = config('card-issuing.cardholder_column', 'cardholder_id');
            $table->string($columnName)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var string */
        $modelName = config('card-issuing.cardholder_model', 'App\User');
        /** @var string */
        $tableName = (new $modelName)->getTable();
        Schema::table($tableName, function (Blueprint $table) {
            /** @var string */
            $columnName = config('card-issuing.cardholder_column', 'cardholder_id');
            $table->dropColumn($columnName);
        });
    }
}
