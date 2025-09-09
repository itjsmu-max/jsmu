<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('employment_assignments')) return;
    Schema::create('employment_assignments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->string('position',100)->nullable();
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->unsignedBigInteger('base_salary')->default(0);
      $table->timestamps();
      $table->index(['employee_id','project_id']);
    });
  }
  public function down(): void { Schema::dropIfExists('employment_assignments'); }
};