<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->binary('symbol');
            $table->string('holding_name');
            $table->decimal('price_purchased_at', 16, 8);
            $table->decimal('trade_size', 16, 8);
            $table->decimal('trade_value', 16, 8);
            $table->date('date_trade_opened');
            $table->date('date_trade_closed')->nullable();
            $table->decimal('price_closed_at', 16, 8)->nullable();
            $table->boolean('trade_opened')->default(1);
            $table->decimal('profit_loss', 16, 2)->nullable();
            $table->boolean('has_screenshots')->default(0);
            $table->bigInteger('user_id')->unsigned()->nullable();
        });

        Schema::table('trades', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
