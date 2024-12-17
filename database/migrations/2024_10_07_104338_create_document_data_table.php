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
        Schema::create('document_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id')->nullable();
            $table->unsignedBigInteger('document_id');
            $table->text('value')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamps();

            // Foreign Keys

            $table->foreign('field_id')
                ->references('id')
                ->on('fields')
                ->onDelete('set null');

            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_data');
    }
};
