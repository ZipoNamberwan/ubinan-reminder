<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('code')->unique();
            $table->string('name');
        });
        Schema::create('villages', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('short_code');
            $table->string('long_code')->unique();
            $table->string('name');
            $table->foreignId('subdistrict_id')->constrained('subdistricts');
        });
        Schema::create('bs', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('short_code');
            $table->string('long_code')->unique();
            $table->string('name');
            $table->foreignId('village_id')->constrained('villages');
        });

        Schema::create('sample_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        Schema::create('commodities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        Schema::create('months', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code');
        });

        Schema::create('years', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        Schema::create('monthly_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('month_id')->constrained('months');
            $table->foreignId('year_id')->constrained('years');
            $table->foreignId('commodity_id')->constrained('commodities');
            $table->foreignId('bs_id')->constrained('bs');
            $table->text('address');
            $table->string('sample_number');
            $table->string('name');
            $table->string('nks');
            $table->foreignId('sample_type_id')->constrained('sample_types');
            $table->integer('reminder_num')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('harvest_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('respondent_name')->nullable();
            $table->text('note')->nullable();
            $table->integer('reminder_num')->default(0);
            $table->unsignedBigInteger('monthly_schedule_id')->unique();
            $table->foreign('monthly_schedule_id')
                ->references('id')
                ->on('monthly_schedules')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('period_settings', function (Blueprint $table) {
            $table->id();
            $table->string('monthly_reminder_before_date');
            $table->string('monthly_reminder_interval');

            $table->string('harvest_reminder_first_before_date');
            $table->string('harvest_reminder_second_before_date');
        });

        Schema::create('sent_messages', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('receiver');
            $table->string('message');
            $table->enum('type', ['monthly', 'harvest']);
            $table->timestamps();
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
