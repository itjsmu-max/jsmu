<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('contract_templates', function (Blueprint $table) {
        $table->boolean('is_active')->default(1)->after('body_html');
    });
}

public function down()
{
    Schema::table('contract_templates', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}

};
