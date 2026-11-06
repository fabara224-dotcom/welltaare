<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graphic_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('company')->nullable();
            $table->string('service');
            $table->text('description');
            $table->unsignedBigInteger('budget')->nullable();
            $table->date('deadline')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('nouvelle');
            $table->unsignedBigInteger('quote_amount')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graphic_requests');
    }
};
