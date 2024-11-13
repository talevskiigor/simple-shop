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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('first');
            $table->string('last');
            $table->string('address');
            $table->string('city');
            $table->text('comment')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('finished')->default(false);
            $table->json('items');
            $table->float('total');
            $table->string('bank_ref')->nullable(); //cPayPaymentRef
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
