<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('vouchers', 'usage_limit')) {
                    $table->unsignedInteger('usage_limit')->nullable()->after('min_subtotal');
                }

                if (!Schema::hasColumn('vouchers', 'used_count')) {
                    $table->unsignedInteger('used_count')->default(0)->after('usage_limit');
                }

                if (!Schema::hasColumn('vouchers', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('is_active');
                }
            });
        }

        if (!Schema::hasTable('voucher_usages')) {
            Schema::create('voucher_usages', function (Blueprint $table) {
                $table->id('id_voucher_usage');
                $table->unsignedBigInteger('id_voucher');
                $table->unsignedBigInteger('id_payment_transaction')->nullable();
                $table->unsignedBigInteger('id_user')->nullable();
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->string('status')->default('reserved');
                $table->timestamp('used_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->timestamps();

                $table->foreign('id_voucher')->references('id_voucher')->on('vouchers')->cascadeOnDelete();
                $table->foreign('id_payment_transaction')->references('id_payment_transaction')->on('payment_transactions')->nullOnDelete();
                $table->foreign('id_user')->references('id')->on('users')->nullOnDelete();
                $table->unique('id_payment_transaction');
                $table->index(['id_voucher', 'status']);
                $table->index(['id_user', 'status']);
            });
        }

        if (Schema::hasTable('vouchers') && Schema::hasTable('voucher_usages')) {
            DB::table('vouchers')
                ->whereNotNull('used_at')
                ->where('used_count', 0)
                ->update(['used_count' => 1]);

            $usedVouchers = DB::table('vouchers')
                ->whereNotNull('used_at')
                ->whereNotNull('used_payment_transaction_id')
                ->get();

            foreach ($usedVouchers as $voucher) {
                $transaction = DB::table('payment_transactions')
                    ->where('id_payment_transaction', $voucher->used_payment_transaction_id)
                    ->first();

                $exists = $transaction
                    ? DB::table('voucher_usages')
                        ->where('id_payment_transaction', $transaction->id_payment_transaction)
                        ->exists()
                    : DB::table('voucher_usages')
                        ->where('id_voucher', $voucher->id_voucher)
                        ->whereNull('id_payment_transaction')
                        ->where('used_at', $voucher->used_at)
                        ->exists();

                if (!$exists) {
                    $userId = null;
                    if (
                        $voucher->used_by_user_id
                        && Schema::hasTable('users')
                        && DB::table('users')->where('id', $voucher->used_by_user_id)->exists()
                    ) {
                        $userId = $voucher->used_by_user_id;
                    }

                    DB::table('voucher_usages')->insert([
                        'id_voucher' => $voucher->id_voucher,
                        'id_payment_transaction' => $transaction->id_payment_transaction ?? null,
                        'id_user' => $userId,
                        'discount_amount' => $transaction->discount_amount ?? 0,
                        'status' => in_array($transaction->transaction_status ?? null, ['settlement', 'capture'], true) ? 'confirmed' : 'reserved',
                        'used_at' => $voucher->used_at,
                        'confirmed_at' => in_array($transaction->transaction_status ?? null, ['settlement', 'capture'], true) ? $voucher->used_at : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');

        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (Schema::hasColumn('vouchers', 'expires_at')) {
                    $table->dropColumn('expires_at');
                }

                if (Schema::hasColumn('vouchers', 'used_count')) {
                    $table->dropColumn('used_count');
                }

                if (Schema::hasColumn('vouchers', 'usage_limit')) {
                    $table->dropColumn('usage_limit');
                }
            });
        }
    }
};
