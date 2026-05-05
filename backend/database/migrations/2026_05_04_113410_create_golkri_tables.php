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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Futsal, Mini Soccer, Big Pitch
            $table->string('match_name');
            $table->dateTime('date_time');
            $table->string('location');
            $table->text('location_url')->nullable();
            $table->integer('quota')->default(14);
            $table->string('price')->default('0');
            $table->text('media_url')->nullable();
            $table->enum('status', ['upcoming', 'finished'])->default('upcoming');
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->unique();
            $table->string('phone_number');
            $table->timestamps();
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('player_name');
            $table->enum('position', ['GK', 'DF', 'MF', 'FW']);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('members');
        Schema::dropIfExists('matches');
    }
};
