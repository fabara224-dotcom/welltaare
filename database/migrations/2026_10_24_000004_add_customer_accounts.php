<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('city', 80)->nullable()->after('phone');
            $table->string('district', 120)->nullable()->after('city');
            $table->string('address')->nullable()->after('district');
        });
        foreach (['orders', 'design_requests', 'graphic_requests'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['orders', 'design_requests', 'graphic_requests'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'city', 'district', 'address']);
        });
    }
};
