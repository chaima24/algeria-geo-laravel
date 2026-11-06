<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dairas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilaya_id')->constrained('wilayas');
            $table->string('name');
            $table->string('arabic_name');
            $table->timestamps();
        });

        Schema::table('communes', function (Blueprint $table) {
            $table->foreignId('daira_id')->nullable()->constrained('dairas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('communes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('daira_id');
        });

        Schema::dropIfExists('dairas');
    }
};
