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
        Schema::table('users', function(Blueprint $table) {
            $table->string('code_org', 5);
            $table->string('ad_fio')->nullable();
            $table->string('ad_post')->nullable();
            $table->string('ad_department')->nullable();
            $table->text('ad_memberof')->nullable();
            $table->string('ad_mail')->nullable();
            $table->string('ad_room')->nullable();
            $table->boolean('ad_disabled')->nullable();
            $table->string('ad_description')->nullable();
            $table->dateTime('last_action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::dropColumns('users', [
            'code_org', 'ad_fio', 'ad_post', 'ad_department', 'ad_memberof', 
            'ad_mail', 'ad_room', 'ad_disabled', 'ad_description', 'last_action'
        ]);
    }
};
