<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeclarationSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('declaration_sent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_id');
            $table->foreignId('user_id');
            $table->string('token');
            $table->json('answers')->nullable();
            $table->boolean('complete');
            $table->boolean('void');
            $table->boolean('sent');
            $table->boolean('short_valid');
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
        Schema::dropIfExists('declaration_sent');
    }
}
