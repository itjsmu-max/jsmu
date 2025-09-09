<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('contract_templates')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('base_salary')->default(0);
            $table->unsignedBigInteger('allowance')->default(0);
            $table->string('location', 191)->nullable();
            $table->string('status', 30)->default('DRAFT');
            $table->string('contract_no', 100)->nullable()->unique();
            $table->string('file_path', 255)->nullable(); // HTML or PDF path
            $table->timestamps();
            $table->index(['status','end_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('contracts'); }
};
