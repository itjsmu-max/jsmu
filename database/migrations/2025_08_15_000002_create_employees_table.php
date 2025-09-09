<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('employees')) return;
    Schema::create('employees', function (Blueprint $table) {
      $table->id();
      $table->string('nik',30)->unique();
      $table->string('full_name',191);
      $table->string('address',255)->nullable();
      $table->string('phone',30)->nullable();
      $table->date('birth_date')->nullable();
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('employees'); }
};