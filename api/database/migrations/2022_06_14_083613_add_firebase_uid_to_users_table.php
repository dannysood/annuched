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

        Schema::table('users', function (Blueprint $table) {
            $driver = Schema::connection($this->getConnection())->getConnection()->getDriverName();
            if ('sqlite' === $driver) {
                // to fix sqlite issue explained at https://stackoverflow.com/questions/20822159/laravel-migration-with-sqlite-cannot-add-a-not-null-column-with-default-value-n
                $table->string('firebase_uid',50)->unique()->default('');
            } else {
                $table->string('firebase_uid',50)->unique();
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
            $table->dropColumn('firebase_uid');
        });
    }
};
