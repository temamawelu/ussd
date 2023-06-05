<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMsisdnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'msisdn')) {
                $table->integer('msisdn')->nullable();
            }
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale')->default('en');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'msisdn')) {
                $table->dropColumn('msisdn');
            }
            if (Schema::hasColumn('users', 'locale')) {
                $table->dropColumn('locale');
            }
        });
    }
}