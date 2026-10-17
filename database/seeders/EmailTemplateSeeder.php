<?php

namespace Database\Seeders;

use App\Enums\EmailTemplateCategory;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Kelas Bimtek Unggulan & Praktis',
                'category' => EmailTemplateCategory::Marketing->value,
                'subject' => 'Upgrade Skill Tim di {instansi}? Yuk Ikuti Bimtek Praktis Ini!',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Semoga aktivitas dan agenda Bapak/Ibu beserta rekan-rekan di <strong>{instansi}</strong> senantiasa lancar dan menyenangkan.</p>

<p>Di tengah dinamika kerja dan pembaruan regulasi yang terus bergulir, tentu kita ingin seluruh tim bisa bekerja lebih percaya diri, cepat, dan terhindar dari kendala administrasi. Kabar baiknya, kami membuka kembali kelas bimbingan teknis praktis yang dirancang khusus untuk menjawab kebutuhan tersebut:</p>

<div style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border-radius: 10px; padding: 22px 24px; margin: 20px 0; color: #ffffff;">
    <span style="background-color: rgba(255,255,255,0.2); font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 8px;">Bimbingan Teknis Spesialis</span>
    <h2 style="margin: 0 0 8px 0; color: #ffffff; font-size: 18px; font-weight: 700;">[Nama Topik Bimtek / Pelatihan Unggulan]</h2>
    <p style="margin: 0; font-size: 14px; opacity: 0.95; line-height: 1.5;">Bedah tuntas regulasi terbaru, simulasi penyusunan dokumen kerja, dan studi kasus riil bersama narasumber praktisi ahli.</p>
</div>

<p><strong>Kenapa rekan-rekan di {instansi} wajib ikut kelas ini?</strong></p>

<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 16px 0;">
    <div style="margin-bottom: 12px; font-size: 14px; color: #334155; line-height: 1.6;">
        <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">💡</span>
        <strong>100% Praktis &amp; Langsung Terap:</strong> Bukan sekadar baca aturan, kita langsung simulasi draf dan bedah contoh kasus riil.
    </div>
    <div style="margin-bottom: 12px; font-size: 14px; color: #2563eb; line-height: 1.6;">
        <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">👨‍🏫</span>
        <strong style="color: #334155;">Narasumber Praktisi Ahli:</strong> Berpengalaman mendampingi instansi dengan gaya penyampaian santai, komunikatif, dan solutif.
    </div>
    <div style="margin-bottom: 12px; font-size: 14px; color: #334155; line-height: 1.6;">
        <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">📜</span>
        <strong>Fasilitas &amp; Dokumen Resmi:</strong> E-Sertifikat bernomor resmi, modul panduan praktis, rekaman sesi (jika online), serta template dokumen siap pakai.
    </div>
    <div style="margin-bottom: 0; font-size: 14px; color: #334155; line-height: 1.6;">
        <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">☕</span>
        <strong>Diskusi &amp; Konsultasi Bebas:</strong> Sesi tanya-jawab leluasa untuk membahas kendala spesifik yang sedang dihadapi di unit kerja Anda.
    </div>
</div>

<table style="width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 14px;">
    <tbody>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 14px; color: #64748b; width: 140px; font-weight: 600;">🗓️ Jadwal Acara</td>
            <td style="padding: 10px 14px; color: #0f172a; font-weight: 600;">[Hari, Tanggal Bulan Tahun] (09.00 - 15.30 WIB)</td>
        </tr>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 14px; color: #64748b; font-weight: 600;">📍 Lokasi / Media</td>
            <td style="padding: 10px 14px; color: #0f172a;">[Hotel Bintang di Jakarta / Yogyakarta] atau [Live Zoom Interactive]</td>
        </tr>
        <tr>
            <td style="padding: 10px 14px; color: #64748b; font-weight: 600;">👥 Kuota Peserta</td>
            <td style="padding: 10px 14px; color: #0f172a;"><span style="color: #d97706; font-weight: 700;">Terbatas untuk 30 peserta</span> (agar bimbingan lebih intensif)</td>
        </tr>
    </tbody>
</table>

<p>Yuk amankan kursi untuk perwakilan {instansi} sekarang sebelum kuota kelas ditutup:</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #2563eb; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #2563eb; border: 1px solid #2563eb; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">📲 Unduh Brosur Lengkap &amp; Daftar &rarr;</a>
        </td>
    </tr>
</table>

<p>Mau tanya-tanya santai seputar silabus atau penawaran rombongan? Tim narahubung kami siap membantu dengan senang hati:<br>
💬 WhatsApp: <strong>0812-xxxx-xxxx</strong> (Admin Kelas Bimtek)<br>
✉️ Email: <strong>info@pelatihan-linkpemda.id</strong></p>

<p>Sampai jumpa di kelas ya Bapak/Ibu!</p>

<p>Salam hangat,<br>
<strong>Tim Program Pelatihan &amp; Bimtek</strong><br>
LinkPemda Portal</p>',
            ],
            [
                'name' => 'Promo Early Bird & Kuota Rombongan',
                'category' => EmailTemplateCategory::Marketing->value,
                'subject' => 'Khusus Rekan {instansi}: Amankan Promo Early Bird Bimtek Bulan Ini!',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Ada kabar gembira untuk Bapak/Ibu dan rekan-rekan di <strong>{instansi}</strong>!</p>

<p>Pendaftaran kelas bimbingan teknis batch terbaru untuk topik <strong>[Nama Topik Bimtek]</strong> resmi kami buka. Spesial untuk minggu ini, kami menghadirkan penawaran khusus agar perencanaan peningkatan kompetensi di instansi Anda makin hemat dan optimal.</p>

<div style="background-color: #f0fdf4; border: 2px dashed #22c55e; border-radius: 10px; padding: 20px 22px; margin: 20px 0; text-align: center;">
    <span style="background-color: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase;">✨ Promo Terbatas Batch Ini</span>
    <h3 style="margin: 10px 0 6px 0; color: #166534; font-size: 20px; font-weight: 800;">Potongan Khusus Early Bird s/d [20%]</h3>
    <p style="margin: 0 0 12px 0; font-size: 14px; color: #15803d;">Berlaku untuk pendaftaran sebelum <strong>[Tanggal Batas Promo]</strong> atau khusus pendaftaran rombongan (min. 3 orang per instansi).</p>
    <div style="display: inline-block; background-color: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 6px 14px; font-size: 13px; color: #166534; font-weight: 600;">
        Kode Promo: <span style="font-family: monospace; font-size: 14px; color: #059669; letter-spacing: 1px;">BIMTEK-HEMAT</span>
    </div>
</div>

<p><strong>Kenapa mendaftar rombongan bareng tim satu unit kerja?</strong></p>
<ul style="font-size: 14px; color: #334155; line-height: 1.8; margin-top: 6px;">
    <li><strong>Pemahaman Seragam:</strong> Tim memiliki satu persepsi yang sama dalam menerapkan alur kerja baru.</li>
    <li><strong>Bonus Konsultasi Eksklusif:</strong> Sesi privat 1 jam khusus bersama narasumber untuk bedah persoalan riil di {instansi}.</li>
    <li><strong>Administrasi Rombongan Lebih Ringkas:</strong> Surat tugas, kuitansi resmi, dan kelengkapan SPPD kami bantu siapkan secara terpadu.</li>
</ul>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #16a34a; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #16a34a; border: 1px solid #16a34a; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">⚡ Klaim Promo &amp; Amankan Kuota Tim &rarr;</a>
        </td>
    </tr>
</table>

<p>Punya pertanyaan seputar draf penawaran resmi untuk pimpinan? Tim kami siap mengirimkan draf undangan resmi dan proposal lengkap langsung ke WhatsApp Anda.</p>

<p>Salam sukses selalu,<br>
<strong>Layanan Konsultasi &amp; Kemitraan Bimtek</strong></p>',
            ],
            [
                'name' => 'In-House Training (Khusus di Kantor)',
                'category' => EmailTemplateCategory::Marketing->value,
                'subject' => 'Solusi Pelatihan Langsung di Kantor {instansi}: Jadwal & Materi Bebas Tentukan!',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Pernahkah rekan-rekan di <strong>{instansi}</strong> merasa butuh pelatihan yang benar-benar fokus membedah persoalan unik di kantor sendiri, tapi terkendala repotnya mengatur perjalanan dinas seluruh tim ke luar kota?</p>

<p>Kini Anda tidak perlu pusing lagi. Melalui program <strong>In-House Training &amp; Pendampingan Eksklusif</strong>, tim narasumber praktisi kami yang akan hadir langsung ke instansi Anda (atau secara daring privat khusus tim Anda):</p>

<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin: 20px 0;">
    <h3 style="margin: 0 0 14px 0; color: #0f172a; font-size: 16px; font-weight: 700;">Keuntungan In-House Training untuk {instansi}:</h3>
    
    <div style="margin-bottom: 14px;">
        <strong style="color: #4338ca; font-size: 14px;">🎯 Materi 100% Sesuai Kebutuhan Nyata</strong>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #475569; line-height: 1.5;">Sebelum pelatihan, kita lakukan <em>pre-assessment</em> santai untuk memetakan kendala dan dokumen kerja riil yang ingin diselesaikan bersama.</p>
    </div>
    
    <div style="margin-bottom: 14px;">
        <strong style="color: #4338ca; font-size: 14px;">📅 Jadwal &amp; Lokasi Fleksibel</strong>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #475569; line-height: 1.5;">Tentukan sendiri hari, jam, dan ruang belajar—bisa di aula kantor, hotel terdekat, atau ruang rapat internal.</p>
    </div>

    <div>
        <strong style="color: #4338ca; font-size: 14px;">💰 Efisiensi Anggaran Jauh Lebih Optimal</strong>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #475569; line-height: 1.5;">Bisa mengikutsertakan lebih banyak staf dalam satu unit kerja tanpa beban biaya transportasi dan akomodasi berlebih ke luar daerah.</p>
    </div>
</div>

<p>Topik yang bisa dipilih sangat beragam, mulai dari penatausahaan keuangan &amp; aset daerah, penyusunan LPJ/SOP, pengadaan barang/jasa, hingga transformasi pelayanan publik digital.</p>

<p>Ingin mendiskusikan topik dan perkiraan anggarannya secara santai?</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #4f46e5; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #4f46e5; border: 1px solid #4f46e5; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">💬 Diskusi Kebutuhan Tim via WhatsApp &rarr;</a>
        </td>
    </tr>
</table>

<p>Semoga solusi ini dapat membantu percepatan kinerja dan kenyamanan tim Bapak/Ibu di {instansi}.</p>

<p>Salam hangat,<br>
<strong>Divisi Pengembangan Kapasitas Kelembagaan</strong></p>',
            ],
            [
                'name' => 'Info Singkat: Tips Praktis & Bedah Aturan',
                'category' => EmailTemplateCategory::Info->value,
                'subject' => '💡 Info Singkat: 3 Hal Penting yang Sering Terlewat saat [Topik Kerja]',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Semoga hari ini menyenangkan! Di sela-sela kesibukan Bapak/Ibu menjalankan tugas di <strong>{instansi}</strong>, kami ingin berbagi rangkuman singkat dan santai seputar hal-hal penting dalam <strong>[Topik / Bidang Kerja Terkait]</strong>.</p>

<p>Dari pengalaman kami mendampingi ratusan instansi, berikut 3 poin krusial yang paling sering terlewatkan namun dampaknya cukup besar:</p>

<div style="background-color: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 18px 0;">
    <div style="margin-bottom: 12px; font-size: 14px; color: #78350f; line-height: 1.6;">
        <strong>1. Keselarasan Bukti Dukung Administratif:</strong><br>
        Sering kali substansi pekerjaan sudah selesai dengan baik, namun format dokumen pendukung belum mengacu pada juknis terbaru sehingga menghambat proses reviu/audit.
    </div>
    <div style="margin-bottom: 12px; font-size: 14px; color: #78350f; line-height: 1.6;">
        <strong>2. Pemutakhiran Data &amp; Input Sistem Berkala:</strong><br>
        Menunda input data menjelang akhir periode sering memicu beban server dan risiko salah entri. Pembagian tugas piket input terbukti menurunkan stres tim hingga 40%.
    </div>
    <div style="margin-bottom: 0; font-size: 14px; color: #78350f; line-height: 1.6;">
        <strong>3. Standardisasi Pemahaman Antar Staf:</strong><br>
        Instruksi pimpinan kerap ditafsirkan berbeda oleh masing-masing seksi. Memiliki lembar kendali (checklist) bersama adalah solusi paling hemat dan efektif.
    </div>
</div>

<div style="background-color: #f1f5f9; border-radius: 8px; padding: 14px 18px; margin: 18px 0; font-style: italic; color: #475569; font-size: 13px;">
    &ldquo;Sering kali kendala di tempat kerja bukan karena aturannya yang rumit, melainkan kita belum menemukan alur praktik kerja yang ringkas dan pas.&rdquo;
</div>

<p><strong>Ingin bedah contoh template &amp; studi kasus riilnya bersama kami?</strong></p>
<p>Dalam kelas bimtek mendatang bertajuk <strong>[Nama Bimtek]</strong>, kita akan kupas tuntas seluruh draf, format checklist, serta simulasi pemecahan masalahnya step-by-step dalam suasana belajar yang santai dan interaktif.</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #d97706; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #d97706; border: 1px solid #d97706; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">📖 Baca Info Lengkap &amp; Jadwal Bimtek &rarr;</a>
        </td>
    </tr>
</table>

<p>Semoga catatan ringkas di atas bermanfaat untuk aktivitas kerja Bapak/Ibu hari ini!</p>

<p>Salam hangat,<br>
<strong>Tim Redaksi &amp; Edukasi Praktisi</strong></p>',
            ],
            [
                'name' => 'Tanya-Jawab Santai Seputar Bimtek',
                'category' => EmailTemplateCategory::Info->value,
                'subject' => 'Tanya-Jawab Santai: Hal-Hal yang Sering Ditanyakan Seputar Bimtek Kami',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Mempersiapkan keikutsertaan pelatihan untuk staf atau pimpinan di <strong>{instansi}</strong> sering kali memunculkan berbagai pertimbangan dan pertanyaan di benak Anda.</p>

<p>Biar nggak bingung atau ragu, berikut kami rangkum tanya-jawab santai seputar pelaksanaan bimbingan teknis yang paling sering ditanyakan oleh rekan-rekan instansi:</p>

<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 18px 0;">
    <div style="margin-bottom: 16px;">
        <h4 style="margin: 0 0 6px 0; color: #1e293b; font-size: 14px; font-weight: 700;">❓ &ldquo;Apakah materi bimtek ini ramah untuk peserta yang masih pemula?&rdquo;</h4>
        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">
            <strong>Tentu saja!</strong> Narasumber kami membawakan materi mulai dari konsep dasar yang mudah dipahami, diikuti contoh studi kasus bertahap tanpa istilah rumit yang membingungkan.
        </p>
    </div>

    <div style="margin-bottom: 16px;">
        <h4 style="margin: 0 0 6px 0; color: #1e293b; font-size: 14px; font-weight: 700;">❓ &ldquo;Bagaimana kelengkapan administrasi untuk SPPD dan LPJ di instansi?&rdquo;</h4>
        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">
            <strong>Sangat aman &amp; lengkap.</strong> Kami siapkan kuitansi resmi, surat undangan bermeterai/stempel, sertifikat bernomor, daftar hadir, serta format pertanggungjawaban yang sesuai standar audit keuangan daerah.
        </p>
    </div>

    <div>
        <h4 style="margin: 0 0 6px 0; color: #1e293b; font-size: 14px; font-weight: 700;">❓ &ldquo;Apakah ada pendampingan setelah sesi bimtek selesai?&rdquo;</h4>
        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">
            <strong>Ya, pasti!</strong> Semua alumni peserta dimasukkan ke dalam grup diskusi eksklusif bersama narasumber untuk wadah konsultasi dan update info regulasi berkala.
        </p>
    </div>
</div>

<p>Masih ada hal lain yang ingin ditanyakan atau ingin meminta draf surat undangan resmi untuk instansi Bapak/Ibu?</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #0284c7; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #0284c7; border: 1px solid #0284c7; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">💬 Tanya Panitia Langsung via WhatsApp &rarr;</a>
        </td>
    </tr>
</table>

<p>Kami siap membantu kelancaran rencana pelatihan Bapak/Ibu kapan pun dibutuhkan.</p>

<p>Salam hangat,<br>
<strong>Helpdesk &amp; Layanan Peserta Bimtek</strong></p>',
            ],
            [
                'name' => 'Sharing Session / Webinar Santai (Free)',
                'category' => EmailTemplateCategory::Agenda->value,
                'subject' => 'Ngobrol Santai: Kupas Tuntas [Topik Tren] Bareng Praktisi (Webinar Gratis)',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Yuk luangkan waktu sejenak sambil ngopi santai untuk mengupas tuntas isu terhangat seputar <strong>[Topik Hangat / Isu Regulasi Baru]</strong> bareng para praktisi dan rekan-rekan aparatur dari berbagai instansi!</p>

<div style="background: linear-gradient(135deg, #0f766e, #0d9488); border-radius: 10px; padding: 22px 24px; margin: 20px 0; color: #ffffff;">
    <span style="background-color: rgba(255,255,255,0.2); font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600; text-transform: uppercase;">Sharing Session Virtual &bull; Free Entry</span>
    <h2 style="margin: 10px 0 6px 0; color: #ffffff; font-size: 18px; font-weight: 700;">Coffee Talk: Kupas Santai [Topik Diskusi]</h2>
    <p style="margin: 0; font-size: 14px; opacity: 0.95;">Diskusi ringan 90 menit penuh wawasan praktis, tips penataan kerja, dan tanya jawab interaktif tanpa rasa canggung.</p>
</div>

<p><strong>Rincian Agenda Santai:</strong></p>
<table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px;">
    <tbody>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 14px; color: #64748b; width: 140px; font-weight: 600;">🗓️ Hari, Tanggal</td>
            <td style="padding: 10px 14px; color: #0f172a; font-weight: 600;">[Hari, Tanggal Bulan Tahun]</td>
        </tr>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 14px; color: #64748b; font-weight: 600;">⏰ Waktu Sesi</td>
            <td style="padding: 10px 14px; color: #0f172a;">10.00 &ndash; 11.30 WIB (90 Menit Efektif)</td>
        </tr>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 14px; color: #64748b; font-weight: 600;">💻 Media Pertemuan</td>
            <td style="padding: 10px 14px; color: #0f172a;">Zoom Meeting (Link dikirim otomatis setelah daftar)</td>
        </tr>
        <tr>
            <td style="padding: 10px 14px; color: #64748b; font-weight: 600;">🎁 Fasilitas Peserta</td>
            <td style="padding: 10px 14px; color: #0d9488; font-weight: 700;">Gratis Biaya + Free E-Certificate + Materi Ringkas</td>
        </tr>
    </tbody>
</table>

<p>Acara ini terbuka untuk seluruh staf dan pejabat di lingkungan <strong>{instansi}</strong>. Silakan bagikan info ini ke grup kerja tim Anda ya!</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #0d9488; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #0d9488; border: 1px solid #0d9488; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">☕ Amankan Kursi Zoom Anda (Gratis) &rarr;</a>
        </td>
    </tr>
</table>

<p>Mari belajar dan bertukar wawasan bersama dengan cara yang menyenangkan.</p>

<p>Salam hangat,<br>
<strong>Komunitas Praktisi &amp; Narasumber Bimtek</strong></p>',
            ],
            [
                'name' => 'Kalender & Agenda Bimtek Bulan Ini',
                'category' => EmailTemplateCategory::Agenda->value,
                'subject' => '📅 Kalender Bimtek & Pelatihan Bulan Ini untuk Rekan {instansi}',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Sedang menyusun rencana keikutsertaan bimbingan teknis atau pelatihan kapasitas staf di <strong>{instansi}</strong> untuk bulan ini?</p>

<p>Biar lebih praktis dan mudah mencocokkan dengan agenda dinas harian, berikut kami hadirkan rangkuman kalender pelatihan unggulan yang siap Anda pilih:</p>

<table style="width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 14px;">
    <thead>
        <tr style="background-color: #f1f5f9; color: #0f172a;">
            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left;">Program / Topik Bimtek</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; width: 140px;">Jadwal</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; width: 130px;">Metode</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">
                <strong>1. Pengelolaan Keuangan &amp; Aset Daerah</strong><br>
                <span style="font-size: 12px; color: #64748b;">Penyusunan LPJ, penatausahaan SIPD, &amp; mitigasi temuan BPK</span>
            </td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Tgl [10-11 Bulan Ini]</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;"><span style="background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Tatap Muka</span></td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">
                <strong>2. Pengadaan Barang &amp; Jasa (PBJ) Pemerintah</strong><br>
                <span style="font-size: 12px; color: #64748b;">Strategi e-Katalog, penyusunan HPS, &amp; mitigasi risiko hukum</span>
            </td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Tgl [17-18 Bulan Ini]</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;"><span style="background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Online / Zoom</span></td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">
                <strong>3. Manajemen Kepegawaian &amp; Penilaian Kinerja ASN</strong><br>
                <span style="font-size: 12px; color: #64748b;">Implementasi SKP terbaru &amp; manajemen talenta instansi</span>
            </td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Tgl [24-25 Bulan Ini]</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;"><span style="background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Tatap Muka</span></td>
        </tr>
    </tbody>
</table>

<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin: 18px 0; font-size: 13px; color: #334155;">
    💡 <em>Catatan: Pelatihan tatap muka diselenggarakan di hotel berbintang dengan fasilitas akomodasi nyaman dan konsumsi lengkap.</em>
</div>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #4338ca; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #4338ca; border: 1px solid #4338ca; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">📥 Unduh Brosur &amp; Jadwal Lengkap (PDF) &rarr;</a>
        </td>
    </tr>
</table>

<p>Mau konsultasi jadwal alternatif atau usulan topik khusus? Jangan ragu hubungi kami ya Bapak/Ibu!</p>

<p>Salam sukses selalu,<br>
<strong>Sekretariat Penyelenggara Bimtek</strong></p>',
            ],
            [
                'name' => 'Pengingat Ramah: Sisa Kuota Terakhir',
                'category' => EmailTemplateCategory::Reminder->value,
                'subject' => 'Hai {nama}, Sisa Kuota Terakhir Bimtek [Nama Topik] Batch Ini!',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Halo Bapak/Ibu <strong>{nama}</strong>,</p>

<p>Sekadar menyapa di sela-sela aktivitas Anda di <strong>{instansi}</strong>. Kami ingin mengabarkan bahwa pendaftaran kelas bimbingan teknis <strong>[Nama Topik Bimtek]</strong> untuk batch bulan ini akan segera kami tutup dalam <strong>[2 hari lagi]</strong>.</p>

<div style="background-color: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid #ea580c; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 18px 0;">
    <h4 style="margin: 0 0 6px 0; color: #9a3412; font-size: 15px; font-weight: 700;">⏰ Kuota Sisa Sedikit Lagi</h4>
    <p style="margin: 0; font-size: 13px; color: #7c2d12; line-height: 1.6;">Saat ini tersisa <strong>[4 kursi peserta lagi]</strong> sebelum pendaftaran kami alihkan ke jadwal batch bulan berikutnya.</p>
</div>

<p>Bila Bapak/Ibu atau tim di instansi sudah berencana untuk bergabung, yuk sempatkan konfirmasi hari ini agar kami dapat langsung mengamankan slot dan menyiapkan kelengkapan berkas kepesertaan Anda:</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 8px; background-color: #ea580c; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #ea580c; border: 1px solid #ea580c; border-radius: 8px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 28px; text-decoration: none;">🏃 Amankan Slot Peserta Sekarang &rarr;</a>
        </td>
    </tr>
</table>

<p>Butuh bantuan konfirmasi cepat? Chat langsung tim admin kami via WhatsApp di <strong>0812-xxxx-xxxx</strong>.</p>

<p>Terima kasih banyak atas perhatiannya, sampai bertemu di kelas!</p>

<p>Salam hangat,<br>
<strong>Tim Narahubung Pelatihan</strong></p>',
            ],
            [
                'name' => 'Undangan Resmi Kedinasan (Formal)',
                'category' => EmailTemplateCategory::Formal->value,
                'subject' => 'Undangan Resmi Bimbingan Teknis Terpadu di Lingkungan Instansi Pemerintah',
                'is_active' => true,
                'is_system' => true,
                'body_content' => '<p>Kepada Yth.<br>
<strong>Bapak/Ibu {nama}</strong><br>
{jabatan} - {instansi}<br>
Di Tempat</p>

<p>Dengan hormat,</p>

<p>Sehubungan dengan upaya peningkatan tata kelola layanan informasi dan kapasitas aparatur di lingkungan instansi pemerintah, bersama ini kami bermaksud mengundang Bapak/Ibu beserta jajaran untuk hadir pada kegiatan:</p>

<div style="background-color: #f8fafc; border-left: 4px solid #2563eb; border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 18px 0;">
    <h3 style="margin: 0 0 6px 0; color: #1e3a8a; font-size: 16px;">Bimbingan Teknis Terpadu &amp; Sosialisasi Layanan Instansi</h3>
    <p style="margin: 0; font-size: 14px; color: #475569;">Fasilitasi sosialisasi, simulasi sistem, dan implementasi layanan digital berkelanjutan.</p>
</div>

<p>Adapun rangkaian kegiatan dijadwalkan sebagai berikut:</p>

<table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px;">
    <thead>
        <tr style="background-color: #f1f5f9;">
            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a; width: 140px;">Waktu / Tanggal</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a;">Agenda Kegiatan</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a;">Keterangan / Tempat</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px; font-weight: 600;">Hari ke-1 (09.00 WIB)</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Pembukaan &amp; Paparan Kebijakan Umum</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Online via Zoom / Hotel Berbintang</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px; font-weight: 600;">Hari ke-2 (09.00 WIB)</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Simulasi Teknis &amp; Pendampingan Sistem</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Online via Zoom / Hotel Berbintang</td>
        </tr>
    </tbody>
</table>

<p>Untuk konfirmasi kehadiran serta mendapatkan tautan registrasi resmi, mohon kesediaan Bapak/Ibu untuk melengkapi formulir melalui tautan di bawah ini:</p>

<table role="presentation" style="margin: 22px 0; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 6px; background-color: #0d5c31; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #0d5c31; border: 1px solid #0d5c31; border-radius: 6px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 600; padding: 12px 28px; text-decoration: none;">Konfirmasi Kehadiran &amp; Registrasi &rarr;</a>
        </td>
    </tr>
</table>

<div style="background-color: #f1f5f9; border-radius: 8px; padding: 16px 20px; margin: 20px 0; border: 1px solid #e2e8f0;">
    <h4 style="margin: 0 0 6px 0; color: #0f172a; font-size: 14px; font-weight: 700;">Narahubung &amp; Layanan Koordinasi:</h4>
    <div style="font-size: 13px; color: #334155; line-height: 1.7;">
        <div>📞 WhatsApp: +62 812-xxxx-xxxx (Admin Panitia)</div>
        <div>✉️ Email: panitia@pemerintah.go.id</div>
    </div>
</div>

<p>Demikian undangan ini kami sampaikan. Atas perhatian dan kerjasama yang baik dari Bapak/Ibu {nama}, kami ucapkan terima kasih.</p>

<p>Hormat kami,<br>
<strong>Panitia Pelaksana Program</strong><br>
Portal Informasi Pemerintah</p>',
            ],
        ];

        foreach ($templates as $tpl) {
            EmailTemplate::updateOrCreate(
                ['name' => $tpl['name']],
                $tpl
            );
        }
    }
}

