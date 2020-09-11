<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockerIdColumnRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', static function (Blueprint $table) {
            $table->foreignId('blocker_id')->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relations', static function (Blueprint $table) {
            $table->dropForeign(['blocker_id']);
            $table->dropColumn(['blocker_id']);
        });
    }
}
