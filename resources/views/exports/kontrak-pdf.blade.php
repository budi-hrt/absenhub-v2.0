<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Kerja - {{ $kontrak->nomor }}</title>
    <style>
        @page {
            margin: 0.8cm 1.8cm 1cm 1.8cm;
            size: 215.9mm 330mm; /* F4 / Folio Presisi */
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.35;
            color: #000;
        }
        .header-box {
            text-align: center;
            margin-bottom: 5px;
        }
        .logo {
            width: 220px;
            margin-bottom: 6px;
        }
        .header-text {
            font-size: 10.5pt;
            font-weight: bold;
            line-height: 1.4;
        }
        .divider {
            border-bottom: 2px solid #000;
            border-top: 0.5px solid #000;
            height: 2px;
            margin-top: 6px;
            margin-bottom: 12px;
        }
        .title-section {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 10pt;
            font-style: italic;
            margin-top: 2px;
        }
        .pasal-title {
            text-align: center;
            font-weight: bold;
            font-style: italic;
            font-size: 11pt;
            margin-top: 10px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .pasal-sub {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        p {
            margin-top: 3px;
            margin-bottom: 4px;
            text-align: justify;
        }
        .indent-p {
            margin-left: 28px;
            text-align: justify;
        }
        .table-data {
            width: 100%;
            margin-bottom: 4px;
            border-collapse: collapse;
        }
        .table-data td {
            padding: 1px 0;
            vertical-align: top;
        }
        ol {
            margin-top: 2px;
            margin-bottom: 4px;
            padding-left: 28px;
        }
        li {
            margin-bottom: 3px;
            text-align: justify;
        }
        .page-break {
            page-break-after: always;
        }
        .ttd-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .ttd-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="header-box">
        <img src="{{ public_path('logo/logo.png') }}" class="logo" alt="Logo" onerror="this.style.display='none'"><br>
        <div class="header-text">
            Telpon : (0451) 486899 / 486889, Email : ptsdpa@gmail.com<br>
            JL. Zebra 1A No.93, KEL. Birobuli Utara, KEC. Palu Selatan, Kota Palu.
        </div>
    </div>
    <div class="divider"></div>

    <!-- JUDUL SURAT -->
    <div class="title-section">
        <div class="doc-title">SURAT PERJANJIAN INTERNSHIP</div>
        <div class="doc-number">Nomor : {{ $kontrak->nomor }}</div>
    </div>

    <p style="margin-left: 15px;">Pada hari ini, {{ $tanggalSuratFormat }} telah dibuat dan disepakati perjanjian kerja antara :</p>

    <!-- PIHAK PERTAMA -->
    <table class="table-data" style="margin-left: 30px; width: 95%;">
        <tr>
            <td style="width: 25px;">1.</td>
            <td style="width: 180px;">Nama</td>
            <td style="width: 15px;">:</td>
            <td>{{ strtoupper($kontrak->penandatangan?->nama_penandatangan ?? 'SHELLY RUDYANI') }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $kontrak->penandatangan?->alamat ?? 'JL. ZEBRA 1A NO.93 PALU' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ strtoupper(is_string($kontrak->penandatangan?->jabatan) ? $kontrak->penandatangan->jabatan : ($kontrak->penandatangan?->jabatan?->nama_jabatan ?? 'DIREKTUR')) }}</td>
        </tr>
    </table>
    <p class="indent-p" style="margin-top: 4px; margin-bottom: 10px;">
        Dalam hal ini bertindak atas nama direksi PT. SINAR DELIMA PANEN ABADI yang berkedudukan di Jl. Zebra 1A No.93 dan selanjutnya disebut <strong>PIHAK PERTAMA</strong>.
    </p>

    <!-- PIHAK KEDUA -->
    <table class="table-data" style="margin-left: 30px; width: 95%;">
        <tr>
            <td style="width: 25px;">2.</td>
            <td style="width: 180px;">Nama</td>
            <td style="width: 15px;">:</td>
            <td>{{ strtoupper($kontrak->karyawan?->nama_karyawan) }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Tempat /Tanggal lahir</td>
            <td>:</td>
            <td>{{ strtoupper($kontrak->karyawan?->tempat_lahir ?? '-') }} {{ $kontrak->karyawan?->tanggal_lahir ? \Carbon\Carbon::parse($kontrak->karyawan->tanggal_lahir)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Pendidikan Terakhir</td>
            <td>:</td>
            <td>{{ strtoupper($kontrak->karyawan?->pendidikan ?? 'S1') }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ ($kontrak->karyawan?->jk_karyawan === 'L' || $kontrak->karyawan?->jk_karyawan === 'Laki-laki') ? 'Laki -laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Agama</td>
            <td>:</td>
            <td>{{ ucfirst($kontrak->karyawan?->agama_karyawan ?? '-') }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $kontrak->karyawan?->alamat_karyawan ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>No. KTP / SIM</td>
            <td>:</td>
            <td>{{ $kontrak->karyawan?->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Telephone</td>
            <td>:</td>
            <td>{{ $kontrak->karyawan?->telp_karyawan ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>No Rek Bank Mandiri</td>
            <td>:</td>
            <td>{{ $kontrak->karyawan?->rekening && $kontrak->karyawan->rekening !== '0' ? $kontrak->karyawan->rekening : '..................................................' }}</td>
        </tr>
    </table>
    <p class="indent-p" style="margin-top: 4px; margin-bottom: 10px;">
        Dalam hal ini bertindak untuk dan atas nama diri sendiri, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>. Kedua belah pihak sepakat untuk mengikatkan diri dalam Perjanjian Kerja Untuk Waktu Tertentu (Magang) dengan ketetentuan-ketentuan sebagai berikut :
    </p>

    <!-- PASAL 1 -->
    <div class="pasal-title">PASAL 1</div>
    <div class="pasal-sub">MASA KERJA DAN GAJI</div>
    
    <table class="table-data" style="margin-left: 30px; width: 95%;">
        <tr>
            <td style="width: 25px;">1.</td>
            <td colspan="3">PIHAK PERTAMA menerima dan memperkerjakan PIHAK KEDUA sebagai :</td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 140px;">- Status</td>
            <td style="width: 15px;">:</td>
            <td>{{ strtoupper($kontrak->masaKontrak?->status_kontrak ?? 'INTERNSHIP ( 1 ) SATU TAHUN') }}</td>
        </tr>
        <tr>
            <td></td>
            <td>- Masa Kontrak</td>
            <td>:</td>
            <td>{{ $tanggalMulaiFormat }} - {{ $tanggalAkhirFormat }}</td>
        </tr>
        <tr>
            <td></td>
            <td>- Jabatan</td>
            <td>:</td>
            <td>{{ $kontrak->karyawan?->jabatan?->nama_jabatan ?? '-' }}</td>
        </tr>
    </table>

    <table class="table-data" style="margin-left: 30px; width: 95%; margin-top: 4px;">
        <tr>
            <td style="width: 25px;">2.</td>
            <td colspan="3">PIHAK KEDUA berhak atas upah / gaji dari pekerjaan yang dilakukannya dari PIHAK PERTAMA sebagai berikut :</td>
        </tr>
        <tr>
            <td></td>
            <td style="width: 140px;">Gaji Pokok</td>
            <td style="width: 15px;">:</td>
            <td>Rp. {{ $kontrak->gaji > 0 ? number_format($kontrak->gaji, 0, ',', '.') : '..................' }} / Bulan.</td>
        </tr>
        <tr>
            <td></td>
            <td>Tunjangan</td>
            <td>:</td>
            <td>Rp. {{ $kontrak->tunjangan > 0 ? number_format($kontrak->tunjangan, 0, ',', '.') : '..................' }} / Bulan ( sesuai posisi/ daerah ).</td>
        </tr>
    </table>

    <table class="table-data" style="margin-left: 30px; width: 95%; margin-top: 4px;">
        <tr>
            <td style="width: 25px;">3.</td>
            <td>PIHAK KEDUA berhak atas uang makan sebesar Rp {{ number_format($kontrak->um_dalamkota, 0, ',', '.') }},-perhari (dalam kota) atau Rp. {{ number_format($kontrak->um_luarkota, 0, ',', '.') }},- perhari (luar kota) sesuai jumlah kehadiran.</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- PASAL 2 -->
    <div class="pasal-title">PASAL 2</div>
    <div class="pasal-sub">JAM KERJA DAN CUTI</div>
    <ol>
        <li>PIHAK KEDUA bersedia menitipkan Ijazah asli kepada PIHAK PERTAMA sebagai jaminan selama bekerja dan akan dikembalikan kepada PIHAK KEDUA bilamana PIHAK KEDUA telah berhenti bekerja. Proses pengembalian ijazah adalah 1 minggu apabila .</li>
        <li>PIHAK KEDUA bersedia bekerja melebihi waktu yang telah ditetapkan apabila diperlukan oleh PIHAK PERTAMA.</li>
        <li>PIHAK KEDUA bersedia ditempatkan dimana saja apabila sewaktu-waktu ditugaskan oleh Perusahaan.</li>
        <li>PIHAK KEDUA berhak memperoleh hak istirahat mingguan selama 1 (satu) hari dalam seminggu.</li>
        <li>Hak cuti timbul setelah PIHAK KEDUA mempunyai masa kerja atau kontrak selama 2 (dua) tahun.</li>
        <li>Jika telah mempunyai masa kerja 2 Tahun maka PIHAK KEDUA akan mendapatkan hak cuti selama 12 (dua belas) hari yang telah ditentukan oleh Perusahaan.</li>
    </ol>

    <!-- PASAL 3 -->
    <div class="pasal-title">PASAL 3</div>
    <div class="pasal-sub">TUGAS DAN TANGGUNG JAWAB</div>
    <ol>
        <li>PIHAK KEDUA bersedia menerima dan melaksanakan tugas dan tanggung jawab tersebut serta tugas-tugas lain yang diberikan oleh PIHAK PERTAMA dengan sebaik-baiknya dan rasa tanggungjawab.</li>
        <li>PIHAK KEDUA bersedia tunduk dan melaksanakan seluruh ketentuan yang telah diatur baik dalam Pedoman Peraturan dan Tata Tertib Karyawan maupun ketentuan lain yang menjadi Keputusan Direksi dan Managemen Perusahaan.</li>
        <li>PIHAK KEDUA bersedia menyimpan dan menjaga kerahasiaan baik dokumen maupun informasi milik PIHAK PERTAMA dan tidak dibenarkan memberikan dokumen atau informasi yang diketahui baik secara lisan maupun tertulis kepada pihak lain.</li>
        <li>PIHAK KEDUA bertanggung jawab penuh terhadap kendaraan dan peralatan kerja PIHAK PERTAMA dan wajib menjaganya dengan sebaik mungkin.</li>
    </ol>

    <!-- PASAL 4 -->
    <div class="pasal-title">PASAL 4</div>
    <div class="pasal-sub">PEMUTUSAN HUBUNGAN KERJA</div>
    <p style="margin-left: 15px;">1. Selama Kontrak berlangsung PIHAK PERTAMA dapat memutuskan hubungan kerja (PHK) dengan PIHAK KEDUA secara sepihak apabila ternyata melakukan kesalahan berat sebagai berikut :</p>
    <table class="table-data" style="margin-left: 35px; width: 93%;">
        <tr>
            <td style="width: 35px;">I.</td>
            <td>PIHAK KEDUA melakukan pelanggaran dari ketentuan pasal 3 Surat Perjanjian Kerja ini setelah sebelumnya mendapat teguran dan peringatan secara patut sesuai dengan prosedur dan ketentuan perusahaan.</td>
        </tr>
        <tr>
            <td>II.</td>
            <td>PIHAK KEDUA tidak dapat menjalankan tugas, target atau sasaran kerja yang telah ditetapkan oleh PIHAK PERTAMA</td>
        </tr>
        <tr>
            <td>III.</td>
            <td>PIHAK KEDUA terlibat baik langsung maupun tidak langsung dalam tindakan pencurian dan atau penggelapan harta/aset perusahaan.</td>
        </tr>
        <tr>
            <td>IV.</td>
            <td>PIHAK KEDUA memberikan keterangan palsu atau yang dipalsukan sehingga merugikan perusahaan.</td>
        </tr>
        <tr>
            <td>V.</td>
            <td>PIHAK KEDUA tidak hadir bekerja selama 5 (lima) hari berturut-turut tanpa pemberitahuan dan atau keterangan dengan bukti yang sah.</td>
        </tr>
        <tr>
            <td>VI.</td>
            <td>PIHAK KEDUA mabuk, meminum minuman keras, memakai atau mengedar narkotika dan zat adiktif lainnya di lingkungan kerja.</td>
        </tr>
        <tr>
            <td>VII.</td>
            <td>PIHAK KEDUA menyerang, menganiaya, mengancam atau mengintimidasi teman sekerja di lingkungan kerja.</td>
        </tr>
        <tr>
            <td>VIII.</td>
            <td>PIHAK KEDUA melakukan perbuatan asusila atau perjudian di lingkungan kerja.</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- PASAL 5 -->
    <div class="pasal-title">PASAL 5</div>
    <div class="pasal-sub">BERAKHIRNYA PERJANJIAN</div>
    <p style="margin-left: 15px;">1. Surat Perjanjian Kerja ini dapat dibatalkan dan atau menjadi tidak berlaku antara lain karena :</p>
    <table class="table-data" style="margin-left: 35px; width: 93%;">
        <tr>
            <td style="width: 30px;">i)</td>
            <td>Jangka waktu yang diperjanjikan sebagaimana tersebut dalam ayat 1 telah berakhir</td>
        </tr>
        <tr>
            <td>ii)</td>
            <td>Diakhiri oleh kedua belah pihak walaupun jangka waktu belum berakhir.</td>
        </tr>
        <tr>
            <td>iii)</td>
            <td>Dilakukan pemutusan hubungan kerja oleh PIHAK PERTAMA karena melakukan pelanggaran terhadap hal-hal sebagaimana diatur dalam Pasal 4 Surat Perjanjian Kerja ini.</td>
        </tr>
        <tr>
            <td>iv)</td>
            <td>PIHAK KEDUA meninggal dunia.</td>
        </tr>
    </table>
    <ol start="2">
        <li>Apabila PIHAK KEDUA berniat mengundurkan diri maka Ia wajib mengajukan surat pengunduran diri kepada PIHAK PERTAMA sekurang-kurangnya 1 (satu) bulan sebelumnya.</li>
        <li>PIHAK PERTAMA tidak berkewajiban untuk memberikan uang pesangon, uang jasa, atau ganti kerugian apapun kepada PIHAK KEDUA setelah berakhirnya masa kerja untuk waktu tertentu (kontrak).</li>
        <li>PIHAK KEDUA wajib mengembalikan seluruh sarana dana prasarana kerja milik PIHAK PERTAMA dalam keadaan baik serta menyelesaikan seluruh tanggung jawab yang diemban PIHAK KEDUA kepada PIHAK PERTAMA pada saat berakhirnya masa kerja waktu tertentu (kontrak) dan berakhirnya hubungan kerja.</li>
    </ol>

    <!-- PASAL 6 -->
    <div class="pasal-title">PASAL 6</div>
    <div class="pasal-sub">PENYELESAIAN PERSELISIHAN</div>
    <ol>
        <li>Apabila terjadi perselisihan antara kedua belah pihak, akan diselesaikan secara musyawarah untuk mencapai muafakat.</li>
        <li>Apabila tidak tercapai kata muafakat, maka kedua belah pihak sepakat untuk menyelesaikan permasalahan tersebut dilakukan melalui prosedur hukum.</li>
    </ol>

    <!-- PASAL 7 -->
    <div class="pasal-title">PASAL 7</div>
    <div class="pasal-sub">PENUTUP</div>
    <ol>
        <li>Surat Perjanjian Kerja untuk Waktu Tertentu ini dibuat dan ditandatangani oleh kedua belah pihak dengan tanpa ada pengaruh dan atau paksaan dari siapapun serta mengikat kedua belah pihak untuk mentaati dan meksanakannya dengan penuh tanggung jawab.</li>
        <li>Apabila dikemudian hari Surat Perjanjian Kerja ini ternyata masih terdapat hal-hal sekiranya bertentangan dengan Peraturan Perundang-undangan Ketenagakerjaan Republik Indonesia dan atau perkembangan Peraturan PT. SINAR DELIMA PANEN ABADI, maka akan diadakan peninjauan dan penyesuaian atas persetujuan kedua belah pihak.</li>
        <li>Demikianlah Surat Perjanjian ini dibuat dan ditandatangan oleh kedua belah pihak di Palu pada tanggal, bulan dan tahun seperti tersebut diatas dalam 1 (satu) rangkap.</li>
    </ol>

    <table class="table-data" style="margin-top: 15px; margin-left: 15px;">
        <tr>
            <td style="width: 100px;">Dibuat di</td>
            <td style="width: 15px;">:</td>
            <td>JL. ZEBRA 1A NO. 93, PALU, SULAWESI TENGAH.</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $tanggalSuratFormat }}</td>
        </tr>
    </table>

    <!-- TANDA TANGAN -->
    <table class="ttd-table">
        <tr>
            <td><strong>PIHAK PERTAMA</strong></td>
            <td><strong>PIHAK KEDUA</strong></td>
        </tr>
        <tr>
            <td style="height: 60px;"></td>
            <td style="height: 60px;"></td>
        </tr>
        <tr>
            <td>( <u><strong>{{ strtoupper($kontrak->penandatangan?->nama_penandatangan ?? 'SHELLY RUDYANI') }}</strong></u> )</td>
            <td>( <u><strong>{{ strtoupper($kontrak->karyawan?->nama_karyawan) }}</strong></u> )</td>
        </tr>
    </table>

</body>
</html>
