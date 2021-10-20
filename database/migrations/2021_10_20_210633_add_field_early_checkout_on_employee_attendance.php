<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldEarlyCheckoutOnEmployeeAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->boolean('is_early_checkout')->default(false)->after('latitude');
            $table->string('lat_checkout')->nullable()->after('is_early_checkout');
            $table->string('long_checkout')->nullable()->after('lat_checkout');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            //
        });
    }
}
