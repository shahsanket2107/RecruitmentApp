<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserprofilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userprofiles', function (Blueprint $table) {
            $table->id();
            $table->biginteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('Education');
            $table->string('Current_Position');
            $table->string('Current_Industry');
            $table->string('Total_Work_Experience');
            $table->string('Last_Working_Month');
            $table->string('Current_Location');
            $table->double('Last_Drawn_Salary');
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
        Schema::dropIfExists('userprofiles');
    }
}
