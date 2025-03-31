<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id('cuti_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti', 'jenis_cuti_id')->onDelete('cascade');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->integer('jumlah');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->enum('status_manager', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->enum('status_hrd', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->date('approved_at_manager')->nullable();
            $table->date('approved_at_hrd')->nullable();
            $table->text('notes_manager')->nullable();
            $table->text('notes_hrd')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};
