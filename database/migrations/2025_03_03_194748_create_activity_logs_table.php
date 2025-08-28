<?php

use App\Domain\Enum\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('customers')->onDelete('cascade');
            $table->string('action_type');
            $table->jsonb('details');
            $table->timestamps();
        });

        Schema::create('activity_log_customer', function (Blueprint $table) {
            $table->primary(['activity_log_id', 'customer_id']);
            $table->foreignId('activity_log_id')->constrained('activity_logs')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('status')->default(StatusEnum::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log_customer', function (Blueprint $table) {
            $table->dropForeign('activity_log_customer_activity_log_id_foreign');
            $table->dropForeign('activity_log_customer_customer_id_foreign');
        });

        Schema::dropIfExists('activity_log_customer');
        Schema::dropIfExists('activity_logs');
    }
};
