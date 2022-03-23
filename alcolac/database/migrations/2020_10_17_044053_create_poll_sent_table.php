<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_sent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id');
            $table->foreignId('user_id');
            $table->string('token');
            $table->text('answer')->nullable();
            $table->boolean('complete');
            $table->boolean('void');
            $table->boolean('sent');
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
        Schema::dropIfExists('poll_sent');
    }
}
