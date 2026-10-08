<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Bab;
use App\Models\Materi;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Never seed demo/admin accounts in production.
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        // Local/testing admin creation is opt-in via an explicit password.
        $adminPassword = env('SEED_ADMIN_PASSWORD');
        if ($adminPassword) {
            User::updateOrCreate(
                ['email' => env('SEED_ADMIN_EMAIL', 'admin@example.com')],
                [
                    'name' => env('SEED_ADMIN_NAME', 'Administrator'),
                    'password' => Hash::make($adminPassword),
                    'role' => 'admin',
                ]
            );
        }

        $bab1 = Bab::firstOrCreate(['urutan'=>1], ['judul'=>'Pengenalan Algoritma','deskripsi'=>'Memahami konsep dasar algoritma dalam kehidupan sehari-hari.']);
        $bab2 = Bab::firstOrCreate(['urutan'=>2], ['judul'=>'Pemrograman Dasar','deskripsi'=>'Mengenal konsep dasar pemrograman dan cara berpikir komputasional.']);
        $bab3 = Bab::firstOrCreate(['urutan'=>3], ['judul'=>'Jaringan Komputer','deskripsi'=>'Mengenal konsep dasar jaringan dan komunikasi antarperangkat.']);

        Materi::firstOrCreate(['bab_id'=>$bab1->id,'urutan'=>1], ['judul'=>'Pengertian Algoritma','isi'=>'Algoritma adalah langkah-langkah yang tersusun secara logis dan sistematis untuk menyelesaikan suatu masalah.\n\nContoh sederhana algoritma dapat ditemukan ketika kita mengikuti langkah-langkah membuat minuman, mengirim pesan, atau mencari rute perjalanan.']);
        Materi::firstOrCreate(['bab_id'=>$bab1->id,'urutan'=>2], ['judul'=>'Ciri-ciri Algoritma','isi'=>'Algoritma yang baik memiliki langkah yang jelas, berurutan, memiliki masukan dan keluaran yang sesuai, serta dapat berhenti setelah sejumlah langkah tertentu.']);
        Materi::firstOrCreate(['bab_id'=>$bab2->id,'urutan'=>1], ['judul'=>'Pengenalan Pemrograman','isi'=>'Pemrograman adalah proses membuat instruksi yang dapat dijalankan komputer untuk menyelesaikan masalah tertentu.']);
        Materi::firstOrCreate(['bab_id'=>$bab3->id,'urutan'=>1], ['judul'=>'Pengertian Jaringan Komputer','isi'=>'Jaringan komputer adalah hubungan antara dua perangkat atau lebih yang memungkinkan pertukaran data dan berbagi sumber daya.']);
    }
}
