<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada user untuk relasi
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $temas = ['Ollama', 'OpenAI', 'Gemini', 'Claude', 'DeepSeek', 'Mistral'];
        
        $projectTitles = [
            'Sistem Monitoring Kualitas Udara Real-Time Kampus IoT',
            'Platform Otomasi Asisten Riset Mahasiswa Menggunakan Agen AI',
            'Portal Rekomendasi Kurikulum Berbasis Model Bahasa Terapan',
            'Aplikasi Deteksi Dini Penyakit Tanaman Pangan Citra Digital',
            'Sistem Prediksi Kelulusan Mahasiswa Berbasis Machine Learning',
            'Integrasi Workflow Asisten Laboratorium Virtual Cerdas',
            'Platform E-Learning Adaptif Berdasarkan Minat dan Bakat Siswa',
            'Sistem Pengenalan Wajah Presensi Mahasiswa Berbasis Edge Computing',
            'Dashboard Analitik Sentimen Media Sosial Terhadap Layanan Publik',
            'Aplikasi Marketplace UMKM Lokal dengan Rekomendasi Pintar',
            'Sistem Pengarsipan Dokumen Terdistribusi Berbasis Blockchain',
            'Otomasi Pengujian Keamanan Web API Menggunakan Fuzzing Agent',
            'Sistem Pendukung Keputusan Seleksi Beasiswa Berbasis AHP-TOPSIS',
            'Aplikasi Manajemen Inventaris Aset Laboratorium Berbasis QR Code',
            'Portal Konseling Virtual Mahasiswa Berbasis AI Percakapan',
            'Sistem Pengenalan Suara Perintah Navigasi Robot Pembersih Lantai',
            'Platform Smart Grid Monitoring Konsumsi Listrik Rumah Tangga',
            'Sistem Klasifikasi Otomatis Pengajuan Proposal Hibah Penelitian',
            'Aplikasi Monitoring Kesehatan Lansia Terhubung Sensor Wearable',
            'Sistem Visualisasi Data Geospasial Daerah Rawan Banjir Kota',
            'Platform Pengelolaan Sampah Pintar Berbasis IoT dan Poin Reward',
            'Sistem Rekomendasi Jalur Evakuasi Kebakaran Gedung Bertingkat',
            'Aplikasi Transkripsi Rapat Otomatis dengan Pengenalan Multi-Pembicara',
            'Dashboard Evaluasi Kinerja Dosen dan Kepuasan Mahasiswa',
            'Sistem Klasifikasi Dokumen Hukum Berbasis Pemrosesan Bahasa Alami',
        ];

        foreach ($projectTitles as $title) {
            $user = $users->random();
            $tema = $temas[array_rand($temas)];

            $project = Project::create([
                'judul' => $title,
                'deskripsi' => $faker->paragraph(3) . ' Target luaran meliputi publikasi ilmiah, prototipe fungsional, dan dokumentasi arsitektur sistem.',
                'tema_agent' => $tema,
                'api_key_secure' => 'sk-' . $faker->regexify('[A-Za-z0-9]{32}'),
                'user_id' => $user->id,
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);

            // Tambahkan activity log untuk proyek
            $project->activityLogs()->create([
                'user_id' => $user->id,
                'action' => 'created',
                'metadata' => ['judul' => $project->judul],
            ]);
        }

        // Seeder Proposal Realistis
        $statuses = ['menunggu', 'disetujui', 'ditolak'];
        $proposalTitles = [
            'Proposal Penelitian Agen Otonom Navigasi Drone Indoor',
            'Pengembangan Modul Smart Home Hemat Energi Berbasis Microcontroller',
            'Implementasi Algoritma Enkripsi Pasca-Kuantum pada Transaksi Digital',
            'Rancang Bangun Prototipe Sistem Pemantau Kebugaran Berbasis Sensor Bioimpedansi',
            'Pengembangan Aplikasi Pelacakan Jejak Karbon Pribadi Harian',
            'Implementasi Model Deteksi Objek Real-Time pada Kamera Pengawas Lalu Lintas',
            'Rancang Bangun Sistem Verifikasi Ijazah Digital Berbasis Hash Kriptografi',
            'Pengembangan Game Edukasi Berbasis Gamifikasi Pembelajaran Pemrograman',
            'Analisis Performa Model Transformer Ringan untuk Perangkat Edge',
            'Sistem Pengenalan Bahasa Isyarat Indonesia Berbasis Sensor Gerak Kamera',
            'Optimalisasi Rute Pengiriman Logistik Menggunakan Algoritma Genetika',
            'Pengembangan Aplikasi Pembelajaran Bahasa Asing Berbasis Chatbot Interaktif',
        ];

        foreach ($proposalTitles as $pTitle) {
            $user = $users->random();
            Proposal::create([
                'user_id' => $user->id,
                'judul' => $pTitle,
                'deskripsi' => $faker->paragraph(2),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
            ]);
        }
    }
}

