<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration fix untuk hosting:
 * Memastikan semua kolom recruitment yang dibutuhkan sudah nullable
 * dan tabel recruitment_posts sudah ada dengan kolom yang benar.
 *
 * Dibuat karena beberapa migration sebelumnya mungkin belum dijalankan
 * di lingkungan shared hosting.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan tabel recruitment_posts ada
        if (! Schema::hasTable('recruitment_posts')) {
            Schema::create('recruitment_posts', function (Blueprint $table) {
                $table->id();
                $table->string('nama_loker');
                $table->enum('posisi', ['PENDAMPING', 'DATA ENTRY', 'ADMIN UMUM']);
                $table->text('deskripsi')->nullable();
                $table->text('jobdesk')->nullable();
                $table->boolean('is_active')->default(false);
                $table->string('slug')->unique();
                $table->json('requirements')->nullable();
                $table->timestamp('tanggal_buka')->nullable();
                $table->timestamp('tanggal_tutup')->nullable();
                $table->string('template_pakta_integritas')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambah kolom yang mungkin belum ada di recruitment_posts
        Schema::table('recruitment_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('recruitment_posts', 'template_pakta_integritas')) {
                $table->string('template_pakta_integritas')->nullable()->after('tanggal_tutup');
            }
            if (! Schema::hasColumn('recruitment_posts', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('template_pakta_integritas');
            }
        });

        // 3. Pastikan kolom recruitment_post_id ada di recruitments
        if (Schema::hasTable('recruitments') && ! Schema::hasColumn('recruitments', 'recruitment_post_id')) {
            Schema::table('recruitments', function (Blueprint $table) {
                $table->foreignId('recruitment_post_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('recruitment_posts')
                      ->onDelete('set null');
            });
        }

        // 4. Pastikan kolom-kolom recruitments sudah nullable
        Schema::table('recruitments', function (Blueprint $table) {
            // foto_diri
            if (Schema::hasColumn('recruitments', 'foto_diri')) {
                $table->string('foto_diri')->nullable()->change();
            }
            // foto_ktp
            if (Schema::hasColumn('recruitments', 'foto_ktp')) {
                $table->string('foto_ktp')->nullable()->change();
            }
            // foto_ijasah
            if (Schema::hasColumn('recruitments', 'foto_ijasah')) {
                $table->string('foto_ijasah')->nullable()->change();
            }
            // pakta_integritas
            if (Schema::hasColumn('recruitments', 'pakta_integritas')) {
                $table->string('pakta_integritas')->nullable()->change();
            }
            // nik
            if (Schema::hasColumn('recruitments', 'nik')) {
                $table->string('nik')->nullable()->change();
            }
            // jenis_kelamin
            if (Schema::hasColumn('recruitments', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable()->change();
            }
            // pengalaman — bisa panjang
            if (Schema::hasColumn('recruitments', 'pengalaman')) {
                $table->text('pengalaman')->nullable()->change();
            }
            // pendidikan_terakhir
            if (Schema::hasColumn('recruitments', 'pendidikan_terakhir')) {
                $table->string('pendidikan_terakhir')->nullable()->change();
            }
            // alamat_lengkap
            if (Schema::hasColumn('recruitments', 'alamat_lengkap')) {
                $table->string('alamat_lengkap', 1000)->nullable()->change();
            }
        });

        // 5. Tambah kolom answers jika belum ada
        if (Schema::hasTable('recruitments') && ! Schema::hasColumn('recruitments', 'answers')) {
            Schema::table('recruitments', function (Blueprint $table) {
                $table->json('answers')->nullable()->after('pakta_integritas');
            });
        }

        // 6. Tambah kolom recruit_type jika belum ada
        if (Schema::hasTable('recruitments') && ! Schema::hasColumn('recruitments', 'recruit_type')) {
            Schema::table('recruitments', function (Blueprint $table) {
                $table->string('recruit_type')->nullable()->after('recruitment_post_id');
            });
        }

        // 7. Tambah kolom foto_ijasah jika belum ada
        if (Schema::hasTable('recruitments') && ! Schema::hasColumn('recruitments', 'foto_ijasah')) {
            Schema::table('recruitments', function (Blueprint $table) {
                $table->string('foto_ijasah')->nullable()->after('foto_ktp');
            });
        }
    }

    public function down(): void
    {
        // Tidak ada rollback destruktif — ini hanya migration perbaikan
    }
};
