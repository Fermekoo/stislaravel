<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTambahanEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('marital_status',['Lajang','Menikah','Duda','Janda'])->default('Lajang')->after('status');
            $table->string('skck')->after('marital_status')->nullable();
            $table->string('ktp')->after('skck')->nullable();
            $table->string('employment_contract')->after('ktp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
}
