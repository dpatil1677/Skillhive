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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('deliverables_note')->nullable()->after('requirements');
            $table->string('deliverables_link')->nullable()->after('deliverables_note');
            $table->string('deliverables_file')->nullable()->after('deliverables_link');
            $table->timestamp('submitted_at')->nullable()->after('deliverables_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['deliverables_note', 'deliverables_link', 'deliverables_file', 'submitted_at']);
        });
    }
};
