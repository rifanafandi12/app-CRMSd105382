/**
 * Reports.js — Chart rendering & filter logic
 */
(function () {
    'use strict';

    // ==============================================
    // CHART CONFIG DEFAULT
    // ==============================================
    const CHART_COLORS = {
        primary:   '#1E65B8',
        primaryBg: 'rgba(30, 101, 184, 0.1)',
        accent:    '#F59E0B',
        accentBg:  'rgba(245, 158, 11, 0.1)',
        success:   '#22c55e',
        successBg: 'rgba(34, 197, 94, 0.1)',
        warning:   '#f59e0b',
        warningBg: 'rgba(245, 158, 11, 0.1)',
        danger:    '#ef4444',
        dangerBg:  'rgba(239, 68, 68, 0.1)',
        neutral:   '#94a3b8',
    };

    const FONT_FAMILY = 'Inter, system-ui, sans-serif';

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    font: { family: FONT_FAMILY, size: 12 },
                    color: '#475569',
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 15,
                },
            },
            tooltip: {
                backgroundColor: '#1F2937',
                padding: 12,
                cornerRadius: 8,
                titleFont: { family: FONT_FAMILY, size: 13, weight: '600' },
                bodyFont:  { family: FONT_FAMILY, size: 12 },
                displayColors: true,
                boxWidth: 8,
                boxHeight: 8,
                boxPadding: 4,
            },
        },
    };

    // ==============================================
    // 1. MONTHLY CHART (multi-line)
    // ==============================================
    function renderMonthlyChart(data) {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Pesan',
                        data: data.messages,
                        borderColor: CHART_COLORS.primary,
                        backgroundColor: CHART_COLORS.primaryBg,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: CHART_COLORS.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Aspirasi',
                        data: data.aspirations,
                        borderColor: CHART_COLORS.accent,
                        backgroundColor: CHART_COLORS.accentBg,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: CHART_COLORS.accent,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Pengguna Baru',
                        data: data.users,
                        borderColor: CHART_COLORS.success,
                        backgroundColor: CHART_COLORS.successBg,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: CHART_COLORS.success,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                ],
            },
            options: {
                ...defaultOptions,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0', drawBorder: false },
                        ticks: { font: { family: FONT_FAMILY, size: 11 }, color: '#94A3B8', precision: 0 },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: FONT_FAMILY, size: 11 }, color: '#94A3B8' },
                    },
                },
            },
        });
    }

    // ==============================================
    // 2. GENDER DOUGHNUT
    // ==============================================
    function renderGenderChart(data) {
        const ctx = document.getElementById('genderChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [data.male, data.female],
                    backgroundColor: [CHART_COLORS.primary, CHART_COLORS.accent],
                    borderWidth: 0,
                    hoverOffset: 8,
                }],
            },
            options: {
                ...defaultOptions,
                cutout: '65%',
                plugins: {
                    ...defaultOptions.plugins,
                    legend: {
                        ...defaultOptions.plugins.legend,
                        position: 'bottom',
                    },
                },
            },
        });
    }

    // ==============================================
    // 3. WEEKLY CHART (bar)
    // ==============================================
    function renderWeeklyChart(data) {
        const ctx = document.getElementById('weeklyChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Pesan',
                        data: data.messages,
                        backgroundColor: CHART_COLORS.primary,
                        borderRadius: 6,
                        barThickness: 'flex',
                        maxBarThickness: 32,
                    },
                    {
                        label: 'Aspirasi',
                        data: data.aspirations,
                        backgroundColor: CHART_COLORS.accent,
                        borderRadius: 6,
                        barThickness: 'flex',
                        maxBarThickness: 32,
                    },
                ],
            },
            options: {
                ...defaultOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0', drawBorder: false },
                        ticks: { font: { family: FONT_FAMILY, size: 11 }, color: '#94A3B8', precision: 0 },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: FONT_FAMILY, size: 11 }, color: '#94A3B8' },
                    },
                },
            },
        });
    }

    // ==============================================
    // 4. ASPIRATION STATUS DOUGHNUT
    // ==============================================
    function renderAspirationChart(data) {
        const ctx = document.getElementById('aspirationChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Dibaca', 'Diproses', 'Selesai'],
                datasets: [{
                    data: [data.pending, data.dibaca, data.diproses, data.selesai],
                    backgroundColor: [
                        CHART_COLORS.warning,
                        CHART_COLORS.primary,
                        CHART_COLORS.accent,
                        CHART_COLORS.success,
                    ],
                    borderWidth: 0,
                    hoverOffset: 8,
                }],
            },
            options: {
                ...defaultOptions,
                cutout: '65%',
                plugins: {
                    ...defaultOptions.plugins,
                    legend: {
                        ...defaultOptions.plugins.legend,
                        position: 'bottom',
                    },
                },
            },
        });
    }

    // ==============================================
    // 5. CLASS DISTRIBUTION (horizontal bar)
    // ==============================================
    function renderClassChart(data) {
        const ctx = document.getElementById('classChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: data.data,
                    backgroundColor: CHART_COLORS.primary,
                    borderRadius: 6,
                    maxBarThickness: 40,
                }],
            },
            options: {
                ...defaultOptions,
                indexAxis: 'y',
                plugins: {
                    ...defaultOptions.plugins,
                    legend: { display: false },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0', drawBorder: false },
                        ticks: { font: { family: FONT_FAMILY, size: 11 }, color: '#94A3B8', precision: 0 },
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: FONT_FAMILY, size: 11 }, color: '#475569' },
                    },
                },
            },
        });
    }

    // ==============================================
    // PERIOD FILTER
    // ==============================================
    function initPeriodFilter() {
        const select = document.getElementById('period-select');
        if (!select) return;

        select.addEventListener('change', function () {
            const period = this.value;

            fetch(window.BASE_URL + 'reports/get_chart_data?period=' + period, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success' && data.data) {
                        // Update URL biar bisa di-share
                        const url = new URL(window.location.href);
                        url.searchParams.set('period', period);
                        window.history.replaceState({}, '', url);

                        // Reload halaman — lebih simpel daripada update semua chart
                        window.location.href = url.toString();
                    }
                })
                .catch(err => console.error('Filter error:', err));
        });
    }

    // ==============================================
    // INIT
    // ==============================================
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.REPORT_DATA) return;

        try {
            renderMonthlyChart(window.REPORT_DATA.monthly);
            renderGenderChart(window.REPORT_DATA.gender);
            renderWeeklyChart(window.REPORT_DATA.weekly);
            renderAspirationChart(window.REPORT_DATA.aspirations);
            renderClassChart(window.REPORT_DATA.classes);
            initPeriodFilter();
        } catch (e) {
            console.error('Chart render error:', e);
        }
    });
})();
