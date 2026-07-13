# PLAN: Halaman Lama Kerja Karyawan

## Tujuan
Halaman read-only untuk melihat lama kerja semua karyawan, berguna untuk:
- Kenaikan gaji / bonus
- THR (gunakan kolom agama)
- Laporan ke HRD / manajemen

## Status
- [ ] Buat halaman `lama-kerja.blade.php`
- [ ] Tambah route di `web.php`
- [ ] Tambah menu sidebar
- [ ] Fitur export (belum ditentukan: maatwebsite/excel + dompdf ATAU print-friendly)

---

## 1. File Baru
`resources/views/pages/karyawan/lama-kerja.blade.php`

Anonymous Livewire class (inline PHP) + blade view.
Pattern sama seperti `karyawan/index.blade.php`.

## 2. Route
Tambah di `routes/web.php` dalam block `middleware('role:admin|super-admin|operator|manager')`:

```php
Route::livewire('/karyawan/lama-kerja', 'pages::karyawan.lama-kerja')->name('karyawan.lama-kerja');
```

## 3. Sidebar
Tambah di `resources/views/components/⚡sidebar-menu.blade.php` setelah menu "Data Karyawan" (line 59):

```blade
<x-menu-item title="Lama Kerja" icon="o-clock" icon-classes="text-info" link="/karyawan/lama-kerja" />
```

## 4. Kolom Tabel

| # | Kolom Key | Label | Sumber Data | Format |
|---|-----------|-------|-------------|--------|
| 1 | no | # | `$loop->iteration` | - |
| 2 | nama | Nama Karyawan | `karyawans.nama_karyawan` | text |
| 3 | nik | NIK | `karyawans.nik` | text |
| 4 | jabatan | Jabatan | `jabatans.nama_jabatan` | text |
| 5 | status_kerja | Status Kerja | `statuses.nama_status` | badge-info |
| 6 | agama | Agama | `karyawans.agama_karyawan` | text |
| 7 | tanggal_masuk | Tanggal Masuk | `karyawans.tanggal_masuk` | d/m/Y |
| 8 | lama_kerja | Lama Kerja | Hitungan PHP | "X Tahun X Bulan" |
| 9 | gaji_pokok | Gaji Pokok | `kontraks.gaji` (kontrak terakhir) | Rupiah (Rp X.XXX) |

## 5. Cara Hitung Lama Kerja

```php
// Hitung dari tanggal_masuk sampai sekarang
$awal = \Carbon\Carbon::parse($karyawan->tanggal_masuk);
$akhir = now();
$diff = $awal->diff($akhir);

// Hasil: "X Tahun X Bulan"
if ($diff->y > 0 && $diff->m > 0) {
    $result = "$diff->y Tahun $diff->m Bulan";
} elseif ($diff->y > 0) {
    $result = "$diff->y Tahun";
} else {
    $result = "$diff->m Bulan";
}
```

Catatan: Untuk karyawan nonaktif, tetap hitung dari `tanggal_masuk` (jangka karir), atau bisa dipilih dari `nonaktif.tanggal_aktif` → `nonaktif.tanggal_nonaktif` (lama kontrak). **TBD**: tanya user prefer yang mana.

## 6. Gaji Pokok

Ambil dari `kontraks` table:
- `Karyawan::with(['kontraks'])` → ambil kontrak terakhir berdasarkan `tanggal_mulai` terbesar
- Tampilkan field `gaji` (integer) dalam format Rupiah

```php
$latestKontrak = $karyawan->kontraks
    ->sortByDesc('tanggal_mulai')
    ->first();
$gajiPokok = $latestKontrak?->gaji ?? 0;
```

Format Rupiah:
```php
'Rp ' . number_format($gajiPokok, 0, ',', '.')
```

## 7. Query / Computed

```php
public function karyawans()
{
    return Karyawan::with(['jabatan', 'status', 'kontraks'])
        ->when($this->search, fn($q) => $q
            ->where('nama_karyawan', 'like', "%{$this->search}%")
            ->orWhere('nik', 'like', "%{$this->search}%"))
        ->when($this->filterJabatan, fn($q) => $q->where('jabatan_id', $this->filterJabatan))
        ->when($this->filterAgama, fn($q) => $q->where('agama_karyawan', $this->filterAgama))
        ->orderBy('nama_karyawan')
        ->paginate(15);
}
```

## 8. Filter

| Filter | Property | Tipe | Sumber |
|--------|----------|------|--------|
| Search | `$search` | string | input nama/NIK |
| Jabatan | `$filterJabatan` | string | `jabatans` table |
| Agama | `$filterAgama` | string | distinct agama_karyawan |

### Filter Lama Kerja (opsional, TBD)
Bisa ditambahkan dropdown:
- Semua
- < 1 Tahun
- 1 - 2 Tahun
- 2 - 5 Tahun
- > 5 Tahun

Cara: filter di PHP setelah query (karena hitungan custom), atau gunakan raw query dengan `TIMESTAMPDIFF`.

## 9. Export (Belum Diputuskan)

### Opsi A: Package (perlu install)
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```
- Export ke Excel (.xlsx) via Livewire Excel
- Export ke PDF via DomPDF
- Lebih profesional, tapi tambah dependency

### Opsi B: Print-friendly (tanpa install)
- Tombol "Print" yang buka `window.print()`
- CSS `@media print` untuk layout bersih
- Bisa langsung save as PDF dari browser
- Cepat, tanpa dependency tambahan

---

## Files Yang Perlu Diubah

| File | Aksi |
|------|------|
| `resources/views/pages/karyawan/lama-kerja.blade.php` | **BARU** - Halaman utama |
| `routes/web.php` | Tambah 1 route |
| `resources/views/components/⚡sidebar-menu.blade.php` | Tambah 1 menu item |

## Referensi
- Model: `app/Models/Karyawan.php` (relations: jabatan, status, kontraks, nonaktifs)
- Model: `app/Models/Kontrak.php` (field: gaji, tunjangan, tanggal_mulai, tanggal_akhir)
- Migration: `database/migrations/2026_07_11_093322_create_kontraks_table.php`
- Pattern: `resources/views/pages/karyawan/index.blade.php` (anonymous Livewire + #[Computed] + with())
