<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTimeConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_time_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->time('check_in')->default(Carbon::createFromFormat('H:i:s', '08:00:00'));
            $table->time('limit_check_in')->default(Carbon::createFromFormat('H:i:s', '09:00:00'));
            $table->time('check_out')->default(Carbon::createFromFormat('H:i:s', '18:00:00'));
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
        Schema::dropIfExists('attendance_time_configs');
    }
}
