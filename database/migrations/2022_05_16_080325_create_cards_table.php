<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kwidoo\CardIssuing\Models\Card;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $modelName = config('card-issuing.card_model');
        $tableName = (new $modelName)->getTable();
        Schema::create($tableName, function (Blueprint $table) {
            $columnName = config('card-issuing.card_holder_foreign_key', 'user_id');
            $table->id();
            $table->foreignId($columnName)->constrained()->onDelete('cascade');
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
        $modelName = config('card-issuing.card_model');
        $tableName = (new $modelName)->getTable();
        Schema::dropIfExists($tableName);
    }
}
