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
        Schema::create('bootcamps', function (Blueprint $table) {
            $table->id('id_bootcamp');
            $table->enum('program_type', ['bootcamp', 'ticketed_event'])->default('bootcamp');
            $table->string('title');
            $table->string('batch_label');
            $table->enum('status', [
                'draft',
                'internal_review',
                'open_registration',
                'published',
                'registration_closed',
                'in_progress',
                'completed',
                'archived',
            ])->default('draft');
            $table->string('mentor_label')->default('0 mentor');
            $table->string('seats_label')->default('0 / 0 kursi');
            $table->string('price_label')->default('Rp 0');
            $table->string('schedule_label')->nullable();
            $table->text('risk_note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bootcamps');
    }
};

