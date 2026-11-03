<div>
    <!-- Page Header & Title -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <h4 class="page-title mb-1 fw-bold">Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="card-body mb-4">
        <div class="card border mb-0">
            <div
                class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark fs-14">
                        <i class="bx bx-bar-chart-alt-2 text-success me-1"></i> Grafik Pengiriman Email
                    </h5>
                    <small class="text-muted">Volume email blast sukses terkirim vs target penerima
                        per bulan</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted fs-12 mb-0 d-none d-sm-inline">Tahun:</label>
                    <select wire:model.live="selectedCampaignYear" class="form-select form-select-sm"
                        style="width: 105px;">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body p-3">
                <div wire:ignore>
                    <div id="campaign-volume-chart" style="min-height: 320px;"></div>
                </div>
            </div>

            <!-- Status Email Marketing di Bawah Grafik (Simple 1 Baris Sejajar dengan Icon) -->
            <div class="card-footer bg-white border-top py-2 px-3">
                <div class="d-flex align-items-center gap-4 flex-wrap fs-13">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-book-content text-primary fs-16"></i>
                        <span class="text-muted">Total Kontak:</span>
                        <span class="fw-bold text-dark">{{ number_format($totalContacts) }}</span>
                        <span class="badge bg-success-subtle text-success fs-10">{{ number_format($activeContacts) }}
                            Aktif</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-broadcast text-info fs-16"></i>
                        <span class="text-muted">Total Campaign:</span>
                        <span class="fw-bold text-dark">{{ number_format($totalCampaigns) }}</span>
                        <span class="badge bg-info-subtle text-info fs-10">{{ number_format($completedCampaigns) }}
                            Selesai</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-check-double text-success fs-16"></i>
                        <span class="text-muted">Blast Terkirim:</span>
                        <span class="fw-bold text-success">{{ number_format($totalMarketingSent) }}</span>
                        @if ($totalMarketingFailed > 0)
                            <span
                                class="badge bg-danger-subtle text-danger fs-10">{{ number_format($totalMarketingFailed) }}
                                Gagal</span>
                        @else
                            <span class="badge bg-success-subtle text-success fs-10">100% Sukses</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body mb-4">
        <div class="card border mb-0">
            <div
                class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark fs-14">
                        <i class="mdi mdi-chart-line text-primary me-1"></i> Grafik Peserta Linkpemda
                    </h5>
                    <small class="text-muted">Jumlah peserta terdaftar dan sertifikat terkirim per
                        bulan (Jan &ndash; Des)</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted fs-12 mb-0 d-none d-sm-inline">Tahun:</label>
                    <select wire:model.live="selectedYear" class="form-select form-select-sm" style="width: 105px;">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body p-3">
                <div wire:ignore>
                    <div id="participant-line-chart" style="min-height: 320px;"></div>
                </div>
            </div>

            <!-- Status Peserta Linkpemda di Bawah Grafik (Simple 1 Baris Sejajar dengan Icon) -->
            <div class="card-footer bg-white border-top py-2 px-3">
                <div class="d-flex align-items-center gap-4 flex-wrap fs-13">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-account-group text-primary fs-16"></i>
                        <span class="text-muted">Total Peserta:</span>
                        <span class="fw-bold text-dark">{{ number_format($totalParticipants) }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-email-check text-success fs-16"></i>
                        <span class="text-muted">Sertifikat Terkirim:</span>
                        <span class="fw-bold text-success">{{ number_format($emailSentCount) }}</span>
                        <span class="badge bg-success-subtle text-success fs-10">{{ $emailSentPercentage }}%</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-clock-outline text-warning fs-16"></i>
                        <span class="text-muted">Belum Dikirim:</span>
                        <span class="fw-bold text-warning">{{ number_format($emailPendingCount) }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-cash-multiple text-info fs-16"></i>
                        <span class="text-muted">Total Biaya:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body mb-4">
        <div class="card border mb-0">
            <div
                class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark fs-14">
                        <i class="mdi mdi-chart-line text-info me-1"></i> Grafik Peserta Tren Media Diklat
                    </h5>
                    <small class="text-muted">Jumlah peserta terdaftar per bulan (Jan &ndash;
                        Des)</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted fs-12 mb-0 d-none d-sm-inline">Tahun:</label>
                    <select wire:model.live="selectedTrenYear" class="form-select form-select-sm" style="width: 105px;">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body p-3">
                <div wire:ignore>
                    <div id="tren-participant-line-chart" style="min-height: 320px;"></div>
                </div>
            </div>

            <!-- Status Peserta Tren Media di Bawah Grafik (Simple 1 Baris Sejajar dengan Icon) -->
            <div class="card-footer bg-white border-top py-2 px-3">
                <div class="d-flex align-items-center gap-4 flex-wrap fs-13">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-account-group text-primary fs-16"></i>
                        <span class="text-muted">Total Peserta:</span>
                        <span class="fw-bold text-dark">{{ number_format($trenTotalParticipants) }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-cash-multiple text-success fs-16"></i>
                        <span class="text-muted">Total Biaya:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($trenTotalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- ApexCharts JS -->
        <script src="{{ asset('assets/admin/libs/apexcharts/apexcharts.min.js') }}"></script>
    @endpush
</div>

@script
<script>
    const ensureApexCharts = (callback) => {
        if (typeof ApexCharts !== 'undefined') {
            callback();
            return;
        }

        let existingScript = document.querySelector('script[src*="apexcharts.min.js"]');
        if (existingScript) {
            existingScript.addEventListener('load', callback, { once: true });
            setTimeout(() => {
                if (typeof ApexCharts !== 'undefined') callback();
            }, 300);
            return;
        }

        const script = document.createElement('script');
        script.src = "{{ asset('assets/admin/libs/apexcharts/apexcharts.min.js') }}";
        script.onload = callback;
        document.head.appendChild(script);
    };

    const destroyAllCharts = () => {
        if (window.__participantApexChart) {
            try {
                window.__participantApexChart.destroy();
            } catch (e) { }
            window.__participantApexChart = null;
        }
        const chartEl = document.getElementById('participant-line-chart');
        if (chartEl) {
            chartEl.innerHTML = '';
        }

        if (window.__trenParticipantApexChart) {
            try {
                window.__trenParticipantApexChart.destroy();
            } catch (e) { }
            window.__trenParticipantApexChart = null;
        }
        const trenChartEl = document.getElementById('tren-participant-line-chart');
        if (trenChartEl) {
            trenChartEl.innerHTML = '';
        }

        if (window.__campaignApexChart) {
            try {
                window.__campaignApexChart.destroy();
            } catch (e) { }
            window.__campaignApexChart = null;
        }
        const campEl = document.getElementById('campaign-volume-chart');
        if (campEl) {
            campEl.innerHTML = '';
        }
    };

    const renderChart = (labels, totalData, sentData) => {
        const el = document.getElementById('participant-line-chart');
        if (!el) return;

        ensureApexCharts(() => {
            const chartEl = document.getElementById('participant-line-chart');
            if (!chartEl || typeof ApexCharts === 'undefined') return;

            // Jika instance sudah ada di memory dan elemen canvas masih ada, cukup updateOptions
            if (window.__participantApexChart && chartEl.querySelector('.apexcharts-canvas')) {
                try {
                    window.__participantApexChart.updateOptions({
                        xaxis: { categories: labels },
                        series: [
                            { name: 'Total Peserta', data: totalData },
                            { name: 'Sertifikat Terkirim', data: sentData }
                        ]
                    });
                    return;
                } catch (e) {
                    if (window.__participantApexChart) {
                        try { window.__participantApexChart.destroy(); } catch (err) { }
                        window.__participantApexChart = null;
                    }
                    chartEl.innerHTML = '';
                }
            } else {
                if (window.__participantApexChart) {
                    try { window.__participantApexChart.destroy(); } catch (err) { }
                    window.__participantApexChart = null;
                }
                chartEl.innerHTML = '';
            }

            const options = {
                chart: {
                    height: 310,
                    type: 'line',
                    fontFamily: 'inherit',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 1,
                        blur: 4,
                        opacity: 0.1
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: [3, 2]
                },
                series: [
                    { name: 'Total Peserta', data: totalData },
                    { name: 'Sertifikat Terkirim', data: sentData }
                ],
                colors: ['#3b82f6', '#10b981'],
                markers: {
                    size: 4,
                    hover: { size: 6 }
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        formatter: (val) => Math.round(val),
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: (val) => val + ' orang'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: { radius: 12 }
                },
                grid: {
                    borderColor: '#e9ecef',
                    strokeDashArray: 4
                }
            };

            window.__participantApexChart = new ApexCharts(chartEl, options);
            window.__participantApexChart.render();
        });
    };

    const renderTrenChart = (labels, totalData) => {
        const el = document.getElementById('tren-participant-line-chart');
        if (!el) return;

        ensureApexCharts(() => {
            const chartEl = document.getElementById('tren-participant-line-chart');
            if (!chartEl || typeof ApexCharts === 'undefined') return;

            if (window.__trenParticipantApexChart && chartEl.querySelector('.apexcharts-canvas')) {
                try {
                    window.__trenParticipantApexChart.updateOptions({
                        xaxis: { categories: labels },
                        series: [
                            { name: 'Total Peserta', data: totalData }
                        ]
                    });
                    return;
                } catch (e) {
                    if (window.__trenParticipantApexChart) {
                        try { window.__trenParticipantApexChart.destroy(); } catch (err) { }
                        window.__trenParticipantApexChart = null;
                    }
                    chartEl.innerHTML = '';
                }
            } else {
                if (window.__trenParticipantApexChart) {
                    try { window.__trenParticipantApexChart.destroy(); } catch (err) { }
                    window.__trenParticipantApexChart = null;
                }
                chartEl.innerHTML = '';
            }

            const options = {
                chart: {
                    height: 310,
                    type: 'line',
                    fontFamily: 'inherit',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 1,
                        blur: 4,
                        opacity: 0.1
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                series: [
                    { name: 'Total Peserta', data: totalData }
                ],
                colors: ['#3b82f6'],
                markers: {
                    size: 4,
                    hover: { size: 6 }
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        formatter: (val) => Math.round(val),
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: (val) => val + ' orang'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: { radius: 12 }
                },
                grid: {
                    borderColor: '#e9ecef',
                    strokeDashArray: 4
                }
            };

            window.__trenParticipantApexChart = new ApexCharts(chartEl, options);
            window.__trenParticipantApexChart.render();
        });
    };

    const renderCampaignChart = (labels, sentRecipients, totalRecipients) => {
        const el = document.getElementById('campaign-volume-chart');
        if (!el) return;

        ensureApexCharts(() => {
            const chartEl = document.getElementById('campaign-volume-chart');
            if (!chartEl || typeof ApexCharts === 'undefined') return;

            if (window.__campaignApexChart && chartEl.querySelector('.apexcharts-canvas')) {
                try {
                    window.__campaignApexChart.updateOptions({
                        xaxis: { categories: labels },
                        series: [
                            { name: 'Email Terkirim', data: sentRecipients },
                            { name: 'Target Penerima', data: totalRecipients }
                        ]
                    });
                    return;
                } catch (e) {
                    if (window.__campaignApexChart) {
                        try { window.__campaignApexChart.destroy(); } catch (err) { }
                        window.__campaignApexChart = null;
                    }
                    chartEl.innerHTML = '';
                }
            } else {
                if (window.__campaignApexChart) {
                    try { window.__campaignApexChart.destroy(); } catch (err) { }
                    window.__campaignApexChart = null;
                }
                chartEl.innerHTML = '';
            }

            const options = {
                chart: {
                    height: 310,
                    type: 'bar',
                    fontFamily: 'inherit',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '32%',
                        borderRadius: 4,
                        borderRadiusApplication: 'end'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [
                    { name: 'Email Terkirim', data: sentRecipients },
                    { name: 'Target Penerima', data: totalRecipients }
                ],
                colors: ['#10b981', '#6366f1'],
                xaxis: {
                    categories: labels,
                    labels: {
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        formatter: (val) => Math.round(val),
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: (val) => val + ' email'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: { radius: 12 }
                },
                grid: {
                    borderColor: '#e9ecef',
                    strokeDashArray: 4
                }
            };

            window.__campaignApexChart = new ApexCharts(chartEl, options);
            window.__campaignApexChart.render();
        });
    };

    // Bersihkan semua chart saat user berpindah halaman via wire:navigate
    document.addEventListener('livewire:navigating', () => {
        destroyAllCharts();
    }, { once: true });

    // Render grafik setelah element siap
    setTimeout(() => {
        renderChart(@json($chartLabels), @json($chartData), @json($chartSentData));
        renderTrenChart(@json($trenChartLabels), @json($trenChartData));
        renderCampaignChart(@json($chartLabels), @json($campaignSentRecipients), @json($campaignTotalRecipients));
    }, 150);

    // Update grafik secara dinamis saat filter tahun berubah
    $wire.on('update-chart', (payload) => {
        const data = Array.isArray(payload) ? (payload[0] || {}) : (payload || {});
        renderChart(data.labels || [], data.totalData || [], data.sentData || []);
    });

    $wire.on('update-tren-chart', (payload) => {
        const data = Array.isArray(payload) ? (payload[0] || {}) : (payload || {});
        renderTrenChart(data.labels || [], data.totalData || []);
    });

    $wire.on('update-campaign-chart', (payload) => {
        const data = Array.isArray(payload) ? (payload[0] || {}) : (payload || {});
        renderCampaignChart(data.labels || [], data.sentRecipients || [], data.totalRecipients || []);
    });
</script>
@endscript