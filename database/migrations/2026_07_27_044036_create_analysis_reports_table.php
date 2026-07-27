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
        Schema::create('analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->string('url'); // Menyimpan URL yang diinput user
        
            // Data dari Google PageSpeed (Lighthouse)
            $table->integer('performance_score')->nullable(); 
            $table->integer('seo_score')->nullable();
        
            // Data dari VirusTotal
            $table->integer('malicious_votes')->nullable(); // Jumlah deteksi virus/malware
            $table->string('security_status')->nullable(); // Status: 'Safe' atau 'Malicious'
        
            // Menyimpan respon utuh dari API jika sewaktu-waktu dibutuhkan (opsional tapi best-practice)
            $table->json('raw_api_data')->nullable();

            $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_reports');
    }
};
