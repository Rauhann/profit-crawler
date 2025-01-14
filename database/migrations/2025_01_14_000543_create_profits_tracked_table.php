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
        Schema::create('profits_tracked', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('crawler_request_id')->constrained('crawler_requests');
            $table->string('company_name');
            $table->double('profit');
            $table->mediumInteger('rank');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profits_tracked');
    }
};
