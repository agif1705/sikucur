<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 /**
  * Run the migrations.
  */
 public function up(): void
 {
  Schema::create('mikrotik_configs', function (Blueprint $table) {
   $table->id();
   $table->string('nagari')->index();
   $table->string('location')->index();
   $table->string('host');
   $table->string('user');
   $table->string('pass');
   $table->integer('port')->default(8728);
   $table->boolean('ssl')->default(false);
   $table->boolean('is_active')->default(true);
   $table->timestamps();

   $table->unique(['nagari', 'location']);
  });
 }

 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::dropIfExists('mikrotik_configs');
 }
};
