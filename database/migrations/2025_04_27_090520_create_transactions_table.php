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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->float('rate');
            $table->unsignedInteger('from')->nullable(false);
            $table->unsignedInteger('to')->nullable(false);
            $table->string('currency');
            $table->string('base_currency');
            $table->string('status')->nullable(false)->default(\App\Domain\Enum\StatusEnum::PENDING->value);
            $table->timestamps();

            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->foreign('from')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('to')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
