<?php

use Illuminate\Database\Migrations\Migration;

class AddNeutrinoUserFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email')->default('/vendor/newelement/neutrino/images/default.png');
            }
			if (!Schema::hasColumn('users', 'api_token')) {
                $table->string('api_token')->nullable();
            }
			if (!Schema::hasColumn('users', 'role_id')) {
            	$table->bigInteger('role_id')->nullable()->after('id');
			}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('avatar');
            });
        }
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('role_id');
            });
        }
    }
}
