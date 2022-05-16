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
        $modelName = config('card-issuing.card_holder_model', 'App\User');
        $tableName = (new $modelName)->getTable();
        Schema::table($tableName, function (Blueprint $table) {
            $columnName = config('card-issuing.card_holder_column', 'card_holder_id');
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
        $modelName = config('card-issuing.card_holder_model', 'App\User');
        $tableName = (new $modelName)->getTable();
        Schema::table($tableName, function (Blueprint $table) {
            $columnName = config('card-issuing.card_holder_column', 'card_holder_id');
            $table->dropColumn($columnName);
        });
    }
}
