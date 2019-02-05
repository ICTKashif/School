<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName',60);
	    $table->string('lastName',60);
	    $table->string('gender',10);
	    $table->string('religion',15);
	    $table->string('bloodgroup',10);
	    $table->string('nationality',50);
	    $table->string('dob',12);
	    $table->string('photo',30);
	    $table->string('phone',150);
	    $table->string('email',250);
	    $table->string('fatherName',180);
	    $table->string('fatherCellNo',15);
             $table->string('presentAddress',500);
 	     $table->string('parmanentAddress',500);
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
        Schema::dropIfExists('teacher');
    }
}
