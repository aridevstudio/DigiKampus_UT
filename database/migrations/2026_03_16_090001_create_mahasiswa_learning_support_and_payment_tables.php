<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id('id_submission');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_material');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->string('file_path')->nullable();
            $table->string('original_file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_dosen')->nullable();
            $table->enum('status', ['submitted', 'reviewed', 'revision_requested'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->foreign('id_material')->references('id_material')->on('course_materials')->cascadeOnDelete();
            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['id_material', 'id_mahasiswa'], 'assignment_submissions_material_student_unique');
        });

        Schema::create('course_instructor_notes', function (Blueprint $table) {
            $table->id('id_course_instructor_note');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_dosen');
            $table->string('judul')->nullable();
            $table->text('konten');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->foreign('id_dosen')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['id_course', 'is_active']);
        });

        Schema::create('course_discussions', function (Blueprint $table) {
            $table->id('id_course_discussion');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_user');
            $table->text('message');
            $table->timestamps();

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->foreign('id_user')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['id_course', 'created_at']);
        });

        Schema::create('support_faqs', function (Blueprint $table) {
            $table->id('id_support_faq');
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id('id_support_ticket');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('answered_by')->nullable();
            $table->string('subject');
            $table->text('question');
            $table->text('faq_suggestion')->nullable();
            $table->text('admin_reply')->nullable();
            $table->enum('status', ['open', 'answered', 'closed'])->default('open');
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('answered_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['id_mahasiswa', 'status']);
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id('id_voucher');
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 12, 2);
            $table->decimal('min_subtotal', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('used_by_user_id')->nullable();
            $table->unsignedBigInteger('used_payment_transaction_id')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('used_by_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id('id_payment_transaction');
            $table->string('order_id')->unique();
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_voucher')->nullable();
            $table->string('payment_provider')->default('midtrans');
            $table->string('preferred_payment_method')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->string('transaction_status')->default('pending');
            $table->string('fraud_status')->nullable();
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('id_voucher')->references('id_voucher')->on('vouchers')->nullOnDelete();
            $table->index(['id_mahasiswa', 'transaction_status']);
        });

        Schema::create('payment_transaction_items', function (Blueprint $table) {
            $table->id('id_payment_transaction_item');
            $table->unsignedBigInteger('id_payment_transaction');
            $table->unsignedBigInteger('id_course');
            $table->string('course_name');
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_payment_transaction')->references('id_payment_transaction')->on('payment_transactions')->cascadeOnDelete();
            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
        });

        DB::table('support_faqs')->insert([
            [
                'question' => 'Bagaimana cara membeli kursus?',
                'answer' => 'Pilih kursus pada halaman Get Courses, tambahkan ke keranjang, lalu lanjutkan ke checkout dan selesaikan pembayaran melalui Midtrans.',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Mengapa tugas saya gagal diunggah?',
                'answer' => 'Pastikan file berformat PDF, DOC, DOCX, atau ZIP dan ukuran file tidak melebihi 10MB. Periksa juga koneksi internet Anda saat mengirim tugas.',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Saya sudah bayar, tapi kursus belum aktif.',
                'answer' => 'Tunggu beberapa saat sampai status pembayaran Midtrans diperbarui. Jika masih belum aktif, kirim pertanyaan melalui Hubungi Support agar admin dapat mengecek transaksi Anda.',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('vouchers')->insert([
            [
                'code' => 'HEMAT10',
                'type' => 'percent',
                'value' => 10,
                'min_subtotal' => 50000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'WELCOME50',
                'type' => 'fixed',
                'value' => 50000,
                'min_subtotal' => 200000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'WEBINAR25',
                'type' => 'percent',
                'value' => 25,
                'min_subtotal' => 100000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['used_by_user_id']);
        });

        Schema::dropIfExists('payment_transaction_items');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('support_faqs');
        Schema::dropIfExists('course_discussions');
        Schema::dropIfExists('course_instructor_notes');
        Schema::dropIfExists('assignment_submissions');
    }
};
