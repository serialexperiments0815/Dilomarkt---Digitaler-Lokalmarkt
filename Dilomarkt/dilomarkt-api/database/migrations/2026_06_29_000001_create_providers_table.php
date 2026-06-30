<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('initials')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->string('type')->nullable();
            $table->integer('since')->nullable();
            $table->boolean('verified')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('providers');
    }
};