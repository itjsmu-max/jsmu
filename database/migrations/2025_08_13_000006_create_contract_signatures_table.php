<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('contract_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->string('signer_role', 30); // Employee, HR
            $table->string('path', 255);
            $table->string('signer_name', 191)->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
            $table->unique(['contract_id','signer_role']);
        });
    }
    public function down(): void { Schema::dropIfExists('contract_signatures'); }
};
