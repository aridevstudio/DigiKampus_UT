<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_seat_reservations', function (Blueprint $table) {
            $table->bigIncrements('id_event_seat_reservation');
            $table->unsignedBigInteger('id_payment_transaction');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->string('status', 16)->default('reserved');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->unique(['id_payment_transaction', 'id_course'], 'event_seat_reservation_transaction_course_unique');
            $table->index(['id_course', 'status', 'expires_at'], 'event_seat_reservation_capacity_index');
            $table->index(['id_mahasiswa', 'id_course', 'status'], 'event_seat_reservation_student_course_index');
            $table->foreign('id_payment_transaction')->references('id_payment_transaction')->on('payment_transactions')->cascadeOnDelete();
            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_seat_reservations');
    }
};
