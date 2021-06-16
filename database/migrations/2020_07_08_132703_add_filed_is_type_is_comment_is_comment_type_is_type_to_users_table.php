<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiledIsTypeIsCommentIsCommentTypeIsTypeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_comment')->default(0)->after('is_profile');
            $table->boolean('is_comment_type')->default(0)->after('is_comment');
            $table->boolean('is_type')->default(0)->after('is_comment_type');
            $table->string('type_role',20)->nullable()->after('is_type');
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
            //
        });
    }
}
