<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat Linkpemda</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .verify-card { max-width: 640px; margin: 60px auto; border-radius: 16px; box-shadow: 0 4px 32px rgba(0,0,0,.08); }
        .badge-lp { font-family: monospace; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-card card p-0 overflow-hidden">
            <div class="card-header py-4 text-center {{ $participant ? 'bg-success text-white' : 'bg-danger text-white' }}">
                @if ($participant)
                    <i class="display-5">✅</i>
                    <h4 class="mt-2 mb-0 fw-bold">Sertifikat Valid & Terverifikasi</h4>
                @else
                    <i class="display-5">❌</i>
                    <h4 class="mt-2 mb-0 fw-bold">Sertifikat Tidak Ditemukan</h4>
                @endif
            </div>

            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <span class="badge bg-light text-dark border badge-lp px-3 py-2">
                        No. Sertifikat: <strong>{{ $certificate_number }}</strong>
                    </span>
                </div>

                @if ($participant)
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted fw-normal">Nama Peserta</th>
                                <td class="fw-bold text-dark">{{ $participant->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Jabatan</th>
                                <td>{{ $participant->position ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Instansi / OPD</th>
                                <td>{{ $participant->institution_name ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Tema Kegiatan</th>
                                <td>{{ $participant->theme ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Tanggal Kegiatan</th>
                                <td>{{ $participant->certificate_date ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Tempat</th>
                                <td>{{ $participant->event_venue ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Lokasi</th>
                                <td>{{ $participant->event_location ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-success-subtle border border-success-subtle text-success text-center mt-2 py-2 fs-13">
                        <small>Sertifikat ini diterbitkan oleh <strong>Linkpemda</strong> dan telah terverifikasi secara digital.</small>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <p>Nomor sertifikat <strong class="font-monospace">{{ $certificate_number }}</strong> tidak terdaftar dalam sistem Linkpemda.</p>
                        <p class="small">Jika Anda merasa ini adalah kesalahan, hubungi panitia Linkpemda.</p>
                    </div>
                @endif
            </div>

            <div class="card-footer text-center text-muted small py-3">
                &copy; {{ date('Y') }} Linkpemda &mdash; Sistem Verifikasi Sertifikat Digital
            </div>
        </div>
    </div>
</body>
</html>

