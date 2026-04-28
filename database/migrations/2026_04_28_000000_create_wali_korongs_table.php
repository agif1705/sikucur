<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
 {
  Schema::create('wali_korongs', function (Blueprint $table) {
   $table->id();
   $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
   $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
   $table->string('name');
   $table->string('wilayah');
   $table->timestamps();

   $table->unique(['nagari_id', 'wilayah']);
  });
 }

 public function down(): void
 {
  Schema::dropIfExists('wali_korongs');
 }
};