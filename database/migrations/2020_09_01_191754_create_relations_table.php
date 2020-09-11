<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relations', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('addressee_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'friendship', 'hate']);
            $table->timestamps();
            $table->unique(['applicant_id', 'addressee_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relations');
    }
}
