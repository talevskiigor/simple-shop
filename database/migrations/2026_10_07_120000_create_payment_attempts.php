<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('reference', 10)->unique();
            $table->string('return_token', 64)->unique();
            $table->unsignedBigInteger('amount_minor');
            $table->boolean('is_test')->default(false);
            $table->string('status')->default('pending');
            $table->string('bank_reference')->nullable()->unique();
            $table->json('request_fields');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payment_attempts'); }
};
