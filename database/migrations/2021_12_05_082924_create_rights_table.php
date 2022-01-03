<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rights', function (Blueprint $table) {
            $table->text('name')->primary();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('refs_rights', function (Blueprint $table) {
            $table->text('right_name')->nullable();
            $table->unsignedBigInteger('rightable_id');
            $table->text('rightable_type');

            $table->foreign('right_name')
                ->references('name')
                ->on('rights')
                ->onDelete('set null')
                ->onUpdate('set null');

            $table->primary(['right_name', 'rightable_id', 'rightable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refs_rights');
        Schema::dropIfExists('rights');
    }
}
