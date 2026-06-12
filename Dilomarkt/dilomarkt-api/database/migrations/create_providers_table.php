<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('initials', 4);
            $table->string('address');
            $table->string('city');
            $table->string('zip', 10);
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->enum('type', ['Fachhandel', 'Baumarkt']);
            $table->unsignedSmallInteger('since');
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('providers');
    }
};
