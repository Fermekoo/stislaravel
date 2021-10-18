<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('division_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('employee_type_id');
            $table->unsignedBigInteger('level_id');
            $table->string('employee_code');
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->string('avatar')->nullable();
            $table->enum('gender',['Laki-Laki','Perempuan']);
            $table->enum('status',['Active','Non-Active'])->default('Active');
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
        Schema::dropIfExists('employees');
    }
}
