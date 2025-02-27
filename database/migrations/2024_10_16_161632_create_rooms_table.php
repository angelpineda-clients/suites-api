<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('rooms', function (Blueprint $table) {
      $table->id();
      $table->string('name')->require();
      $table->string('slug');
      $table->text('description')->nullable();
      $table->integer('capacity')->nullable();
      $table->integer('beds')->nullable();
      $table->double('price')->require();
      $table->foreignId('size_id')->nullable()->constrained()->onDelete('cascade');
      $table->foreignId('floor_id')->nullable()->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists(table: 'rooms');
  }
};
