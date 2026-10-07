<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // No existing account is implicitly promoted. Use admin:manage to provision it.
        Schema::table('users', fn (Blueprint $table) => $table->boolean('is_admin')->default(false)->index());
        Schema::table('pages', fn (Blueprint $table) => $table->boolean('published')->default(true));
        Schema::table('media', function (Blueprint $table) {
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('bytes')->nullable();
            $table->string('sha256', 64)->nullable()->index();
            $table->string('alt')->nullable();
        });
        Schema::table('media_product', fn (Blueprint $table) => $table->unsignedInteger('position')->default(0));
        Schema::create('media_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('path', 1024);
            $table->string('target', 1024);
            $table->string('path_hash', 64)->unique();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change();
            $table->decimal('discount', 5, 2)->nullable()->change();
        });
        Schema::table('orders', fn (Blueprint $table) => $table->decimal('total', 12, 2)->change());
    }
    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn('published'));
        Schema::table('media', fn (Blueprint $table) => $table->dropColumn(['mime', 'bytes', 'sha256', 'alt']));
        Schema::dropIfExists('media_aliases');
        Schema::table('media_product', fn (Blueprint $table) => $table->dropColumn('position'));
        // Money columns intentionally retain the safer decimal representation.
    }
};
