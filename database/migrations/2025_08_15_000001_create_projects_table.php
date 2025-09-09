<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('projects')) return;
    Schema::create('projects', function (Blueprint $table) {
      $table->id();
      $table->string('code',50)->unique();
      $table->string('name',191);
      $table->string('address',255)->nullable();
      $table->string('location',191)->nullable();
      $table->decimal('latitude',10,7)->nullable();
      $table->decimal('longitude',10,7)->nullable();
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('projects'); }
};