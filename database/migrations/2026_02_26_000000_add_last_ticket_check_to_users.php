<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastTicketCheckToUsers extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'last_ticket_check')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_ticket_check')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_ticket_check');
        });
    }
}
