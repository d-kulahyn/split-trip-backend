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
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('simplify_debts')->default(true);
            $table->string('name')->nullable(false);
            $table->string('category')->nullable(false);
            $table->string('final_currency')->nullable(false);
            $table->string('avatar')->nullable();
            $table->unsignedInteger('created_by')->nullable(false);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::create('customer_group', function (Blueprint $table) {
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            $table->primary(['group_id', 'customer_id']);
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable(false);
            $table->string('category')->nullable(false);
            $table->string('final_currency')->nullable(false);
            $table->timestamps();

            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
        });

        Schema::create('expense_debts', function (Blueprint $table) {
            $table->id();
            $table->float('amount')->nullable(false);
            $table->string('currency')->nullable(false);
            $table->unsignedInteger('from')->nullable(false);
            $table->unsignedInteger('expense_id')->nullable()->default(null);
            $table->unsignedInteger('to')->nullable(false);
            $table->string('status')->nullable(false)->default(\App\Domain\Enum\DebtStatusEnum::PENDING->value);
            $table->timestamps();

            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('from')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('to')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::create('expense_payers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('expense_id')->nullable(false);
            $table->float('amount')->nullable(false);
            $table->string('currency')->nullable(false);
            $table->unsignedInteger('payer_id')->nullable(false);
            $table->timestamps();

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('payer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::create('expense_debtors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('expense_id')->nullable(false);
            $table->unsignedInteger('debtor_id')->nullable(false);
            $table->string('currency')->nullable(false);
            $table->float('amount')->nullable(false);
            $table->timestamps();

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('debtor_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_debts');
        Schema::dropIfExists('expense_payers');
        Schema::dropIfExists('expense_debtors');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('customer_group');
        Schema::dropIfExists('groups');
    }
};
