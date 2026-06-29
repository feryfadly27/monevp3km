# Rancangan Konsep Sistem Monev Penelitian & Pengabdian Masyarakat (P3KM)

**Tujuan sistem:** memantau (monitoring) dan menilai (evaluasi) pelaksanaan kegiatan penelitian dan pengabdian masyarakat yang sudah didanai, sampai kegiatan selesai.

Sistem ini **bukan** sistem pengajuan/seleksi proposal. Fokusnya hanya: data kegiatan masuk → dipantau → dinilai reviewer → direkap.

---

## 1. Peran (Roles) — siapa melakukan apa

| Peran | Tugas utama | Tidak boleh |
|---|---|---|
| **Admin / LPPM** | Kelola skema; input/upload data kegiatan per skema; assign reviewer ke tiap kegiatan; pantau seluruh progres; tarik rekap/laporan. | Tidak menilai kegiatan. |
| **Ketua Kegiatan (Dosen/Peneliti)** | Upload laporan kemajuan + bukti luaran sesuai jadwal monev. | Tidak melihat penilaian reviewer (kecuali catatan yang dibuka admin). |
| **Reviewer** | Menilai kegiatan yang ditugaskan: beri skor per kriteria, catatan, dan rekomendasi (lanjut / perbaikan / dihentikan). | Hanya melihat kegiatan yang di-assign ke dirinya. |

> **Catatan penting tentang kebingungan "alur & peran":**
> Peran Ketua Kegiatan itu **opsional**. Ada 2 model:
> - **Model A (lebih ringan):** Admin yang input semua data + laporan. Hanya ada Admin + Reviewer. Cocok kalau jumlah kegiatan sedikit / dosen tidak mau repot login.
> - **Model B (lebih lengkap):** Dosen login sendiri untuk upload laporan kemajuan. Beban admin lebih ringan, data lebih real-time, tapi butuh sosialisasi ke dosen.
>
> Saran: mulai dari **Model A** dulu (paling sederhana), naik ke Model B saat sudah jalan.

---

## 2. Struktur skema (Penelitian & Pengmas terpisah)

Dua **kategori besar**, masing-masing punya beberapa **skema**:

```
PENELITIAN
 ├─ Penelitian Dasar
 ├─ Penelitian Terapan
 └─ Penelitian Dosen Pemula
        ... (sesuaikan)

PENGABDIAN MASYARAKAT
 ├─ Pengabdian Kemitraan Masyarakat
 ├─ Pengabdian Berbasis Produk
 └─ Pengabdian Dosen Pemula
        ... (sesuaikan)
```

Tiap skema sebaiknya menyimpan aturan sendiri: **dana maksimal**, **target luaran wajib** (mis. publikasi jurnal / HKI / produk), dan **jadwal monev**. Ini penting karena reviewer menilai kegiatan **berdasarkan target skema-nya**, bukan target umum.

---

## 3. Alur status kegiatan (inti monitoring)

Setiap kegiatan bergerak melalui status berikut:

```
[1] Terdaftar      → admin selesai input kegiatan + assign reviewer
      │
[2] Berjalan       → kegiatan sedang dilaksanakan
      │
[3] Laporan Masuk  → laporan kemajuan/akhir + bukti luaran sudah diunggah
      │
[4] Dinilai        → reviewer sudah memberi skor & rekomendasi
      │
[5] Selesai        → monev tuntas, hasil terekap
```

Status inilah yang "dipantau" di dashboard admin (berapa kegiatan di tiap status, mana yang telat, dsb).

---

## 4. Data yang disimpan per kegiatan

- **Identitas:** judul, kategori (penelitian/pengmas), skema, tahun pelaksanaan
- **Tim:** ketua (nama, NIDN, prodi/fakultas), anggota
- **Pendanaan:** sumber dana, jumlah dana
- **Target luaran:** sesuai skema (publikasi/HKI/produk/laporan)
- **Berkas:** file laporan kemajuan, laporan akhir, bukti luaran
- **Status pelaksanaan:** (lihat poin 3)
- **Penugasan:** reviewer yang ditunjuk
- **Hasil monev:** skor, catatan, rekomendasi

---

## 5. Proses penilaian reviewer

Saat monev, reviewer mengisi **borang penilaian** berisi beberapa kriteria berbobot. Contoh kriteria umum:

| Kriteria | Bobot (contoh) |
|---|---|
| Kesesuaian pelaksanaan dengan proposal | 25% |
| Capaian luaran / target skema | 30% |
| Kemajuan & ketepatan waktu | 20% |
| Penggunaan dana / kewajaran | 15% |
| Kualitas laporan & bukti | 10% |

Setiap kriteria diberi skor (mis. 1–7 atau 1–100), lalu **skor akhir = Σ(skor × bobot)**. Reviewer juga memberi:
- **Catatan/saran** (teks bebas)
- **Rekomendasi:** Lanjut / Perlu Perbaikan / Dihentikan

Hasil semua kegiatan bisa direkap admin menjadi laporan per skema, per fakultas, atau per tahun.

---

## 6. Halaman/menu yang dibutuhkan (gambaran minimal)

- **Admin:** Dashboard status, Kelola Skema, Kelola Kegiatan (input/upload), Assign Reviewer, Rekap & Laporan, Kelola User.
- **Reviewer:** Daftar Tugas Penilaian, Form Penilaian, Riwayat.
- **(Model B) Dosen:** Daftar Kegiatan Saya, Upload Laporan.

---

---

## 7. Rekomendasi teknis (jawaban kebingungan "mau dibangun pakai apa")

Ada 3 jalur, urut dari paling cepat ke paling fleksibel:

### Opsi A — No-code (paling cepat jadi)
Pakai **Google Sheets + Google Forms + Looker Studio**, atau platform seperti **AppSheet / Airtable**.
- ➕ Bisa jalan dalam hitungan hari, tanpa programmer, gratis/murah.
- ➕ Admin input via Sheet, reviewer isi via Form, dashboard otomatis di Looker Studio.
- ➖ Kontrol akses/hak per peran terbatas, kurang rapi untuk skala besar.
- **Cocok kalau:** ingin cepat jalan, jumlah kegiatan puluhan–ratusan, tim kecil.

### Opsi B — Low-code / platform jadi
Pakai **WordPress + plugin form/manajemen**, atau platform low-code seperti **Budibase / NocoDB**.
- ➕ Lebih rapi soal login & peran, masih relatif cepat.
- ➖ Tetap ada batas kustomisasi; perlu sedikit kemampuan teknis.
- **Cocok kalau:** butuh login per peran yang jelas tapi belum mau bangun dari nol.

### Opsi C — Custom web app (paling fleksibel) ⭐ rekomendasi jangka panjang
Bangun aplikasi sendiri. Stack yang umum & mudah dicari SDM-nya di Indonesia:
- **Laravel (PHP) + MySQL + Bootstrap/Tailwind** — paling banyak dipakai kampus, banyak contoh sistem LPPM.
- Alternatif: **Next.js / React + Node + PostgreSQL**, atau **Python (Django/Laravel-like)**.
- ➕ Hak akses per peran, alur status, upload berkas, rekap otomatis — semua bisa persis kebutuhan.
- ➖ Butuh waktu pengembangan & programmer.
- **Cocok kalau:** ini akan jadi sistem resmi LPPM yang dipakai bertahun-tahun.

### Saran bertahap
1. **Fase 1 (sekarang):** validasi alur pakai **Opsi A** — buktikan prosesnya benar dulu.
2. **Fase 2:** kalau alur sudah mantap, bangun **Opsi C (Laravel)** sebagai sistem resmi.

Mulai kecil, perbaiki alur dulu, baru investasi ke sistem besar. Ini menghindari salah bangun di awal.

---

## 8. Langkah berikutnya yang bisa saya bantu
- Buat **ERD / rancangan tabel database** (kegiatan, skema, user, penilaian).
- Buat **mockup tampilan** halaman admin & reviewer.
- Buat **template borang penilaian** (Excel/Form) siap pakai untuk Opsi A.
- Buat **kerangka aplikasi Laravel** untuk Opsi C.

Tinggal bilang mau yang mana.

