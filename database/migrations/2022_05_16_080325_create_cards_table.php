<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var string */
        $modelName = config('card-issuing.card_model');
        /** @var string */
        $tableName = (new $modelName)->getTable();
        Schema::create($tableName, function (Blueprint $table) use ($modelName) {
            $columnName = config('card-issuing.cardholder_foreign_key', 'user_id');
            $table->id();
            $table->foreignId($columnName)->constrained()->onDelete('cascade');
            $table->string('stripe_card_id')->nullable()->unique();
            $table->string('brand')->nullable();
            $table->enum('cancellation_reason', [$modelName::STATUS_CANCELED_LOST, $modelName::STATUS_CANCELED_STOLEN])->nullable();
            $table->enum('status', [$modelName::STATUS_ACTIVE, $modelName::STATUS_INACTIVE, $modelName::STATUS_BLOCKED])->nullable();
            $table->enum('type', [$modelName::TYPE_PHYSICAL, $modelName::TYPE_VIRTUAL])->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('last4', 4)->nullable();
            $table->smallInteger('exp_month')->nullable();
            $table->smallInteger('exp_year')->nullable();
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
        /** @var string */
        $modelName = config('card-issuing.card_model');
        /** @var string */
        $tableName = (new $modelName)->getTable();
        Schema::dropIfExists($tableName);
    }
}
