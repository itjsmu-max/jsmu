<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('contract_templates')) return;
    Schema::create('contract_templates', function (Blueprint $table) {
      $table->id();
      $table->string('name',191)->unique();
      $table->longText('body_html');
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('contract_templates'); }
};