<?php
// app/views/admin/dashboard.php
/** @var array $stats @var string $from @var string $to */
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Dashboard</h1>
        <p class="admin-page-sub">Last 30 days — <?= date('M d', strtotime($from)) ?> to <?= date('M d, Y', strtotime($to)) ?></p>
    </div>
</div>

<!-- ── Stat Cards ──────────────────────────── -->
<div class="dash-stats">

    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Revenue (30 days)</span>
            <span class="stat-value">₱<?= number_format($stats['revenue'], 2) ?></span>
            <span class="stat-sub">₱<?= number_format($stats['total_revenue'], 2) ?> all time</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
            </svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Orders (30 days)</span>
            <span class="stat-value"><?= number_format($stats['orders']) ?></span>
            <span class="stat-sub">Total placed</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon--purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Customers (30 days)</span>
            <span class="stat-value"><?= number_format($stats['customers']) ?></span>
            <span class="stat-sub">Unique buyers</span>
        </div>
    </div>

    <div class="stat-card <?= $stats['low_stock'] > 0 ? 'stat-card--warn' : '' ?>">
        <div class="stat-icon stat-icon--orange">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Low / Out of Stock</span>
            <span class="stat-value"><?= $stats['low_stock'] ?></span>
            <span class="stat-sub">
                <?php if ($stats['low_stock'] > 0): ?>
                    <a href="<?= APP_URL ?>/admin/inventory">View inventory →</a>
                <?php else: ?>
                    All stocked up
                <?php endif; ?>
            </span>
        </div>
    </div>

</div>

<!-- ── Row 1: Sales + Revenue line charts ──── -->
<div class="dash-row">
    <div class="admin-card dash-chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Sales Over Time</h3>
            <span class="chart-sub">Orders per day</span>
        </div>
        <canvas id="salesChart" height="120"></canvas>
    </div>

    <div class="admin-card dash-chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Revenue Over Time</h3>
            <span class="chart-sub">Daily revenue (₱)</span>
        </div>
        <canvas id="revenueChart" height="120"></canvas>
    </div>
</div>

<!-- ── Row 2: Best Sellers + Order Status ─── -->
<div class="dash-row">
    <div class="admin-card dash-chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Best Sellers</h3>
            <span class="chart-sub">Units sold (30 days)</span>
        </div>
        <canvas id="bestSellersChart" height="160"></canvas>
    </div>

    <div class="admin-card dash-chart-card dash-chart-card--sm">
        <div class="chart-header">
            <h3 class="chart-title">Orders by Status</h3>
            <span class="chart-sub">All time</span>
        </div>
        <div class="donut-wrap">
            <canvas id="statusChart"></canvas>
        </div>
        <div id="statusLegend" class="donut-legend"></div>
    </div>
</div>

<!-- ── Row 3: Inventory stock bar ────────── -->
<div class="admin-card" style="margin-top:1.5rem">
    <div class="chart-header">
        <h3 class="chart-title">Inventory Stock Levels</h3>
        <span class="chart-sub">Current stock per product</span>
    </div>
    <canvas id="inventoryChart" height="100"></canvas>
</div>

<!-- ── Styles ─────────────────────────────── -->
<style>
.dash-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: var(--color-white, #fff);
    border: 1px solid var(--color-border, #e5e7eb);
    border-radius: 12px;
    padding: 1.2rem 1.4rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    transition: box-shadow .2s;
}
.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }
.stat-card--warn { border-color: #f97316; background: #fff7ed; }
.stat-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon--green  { background:#dcfce7; color:#16a34a; }
.stat-icon--blue   { background:#dbeafe; color:#2563eb; }
.stat-icon--purple { background:#ede9fe; color:#7c3aed; }
.stat-icon--orange { background:#ffedd5; color:#ea580c; }
.stat-body { display:flex; flex-direction:column; gap:2px; min-width:0; }
.stat-label { font-size:.75rem; color:var(--color-muted,#6b7280); font-weight:500; text-transform:uppercase; letter-spacing:.04em; }
.stat-value { font-size:1.6rem; font-weight:700; color:var(--color-text,#111); line-height:1.1; }
.stat-sub   { font-size:.75rem; color:var(--color-muted,#6b7280); }
.stat-sub a { color:var(--color-forest,#2d5a27); text-decoration:underline; }

.dash-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
@media (max-width: 900px) { .dash-row { grid-template-columns: 1fr; } }

.dash-chart-card { padding: 1.4rem; }
.dash-chart-card--sm { max-width: 100%; }

.chart-header { margin-bottom: 1rem; }
.chart-title  { font-size: 1rem; font-weight: 600; color: var(--color-text, #111); margin:0 0 2px; }
.chart-sub    { font-size: .75rem; color: var(--color-muted, #6b7280); }

.donut-wrap { max-width: 220px; margin: 0 auto; }
.donut-legend {
    display: flex; flex-wrap: wrap; gap: .4rem .8rem;
    margin-top: .8rem; justify-content: center;
}
.donut-legend-item {
    display: flex; align-items: center; gap: .3rem;
    font-size: .75rem; color: var(--color-text, #111);
    text-transform: capitalize;
}
.donut-legend-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}
</style>

<!-- ── Chart.js ───────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(async function () {

    const BASE = '<?= APP_URL ?>';
    const FROM = '<?= $from ?>';
    const TO   = '<?= $to ?>';

    // Shared style helpers
    const gridColor  = 'rgba(0,0,0,.06)';
    const fontColor  = '#6b7280';
    const forestGreen = '#2d5a27';
    const softGreen   = 'rgba(45,90,39,.12)';

    function lineDefaults(label, color, fill = true) {
        return {
            label,
            borderColor    : color,
            backgroundColor: fill ? color.replace(')', ',.12)').replace('rgb', 'rgba') : 'transparent',
            borderWidth    : 2.5,
            pointRadius    : 3,
            pointHoverRadius: 5,
            tension        : 0.4,
            fill,
        };
    }

    function axesDefaults() {
        return {
            x: {
                grid : { color: gridColor },
                ticks: { color: fontColor, font: { size: 11 }, maxTicksLimit: 10 },
            },
            y: {
                grid : { color: gridColor },
                ticks: { color: fontColor, font: { size: 11 } },
                beginAtZero: true,
            },
        };
    }

    // ── 1. Sales line chart ───────────────────
    try {
        const res  = await fetch(`${BASE}/admin/api/sales-chart?from=${FROM}&to=${TO}`);
        const json = await res.json();

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels  : json.labels,
                datasets: [{ ...lineDefaults('Orders', forestGreen), data: json.data }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales : axesDefaults(),
            },
        });
    } catch (e) { console.error('Sales chart:', e); }

    // ── 2. Revenue line chart ─────────────────
    try {
        const res  = await fetch(`${BASE}/admin/api/revenue-chart?from=${FROM}&to=${TO}`);
        const json = await res.json();

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels  : json.labels,
                datasets: [{ ...lineDefaults('Revenue (₱)', '#7c3aed'), data: json.data }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales : {
                    ...axesDefaults(),
                    y: {
                        ...axesDefaults().y,
                        ticks: {
                            ...axesDefaults().y.ticks,
                            callback: v => '₱' + v.toLocaleString(),
                        },
                    },
                },
            },
        });
    } catch (e) { console.error('Revenue chart:', e); }

    // ── 3. Best sellers bar chart ─────────────
    try {
        const res  = await fetch(`${BASE}/admin/api/best-sellers?from=${FROM}&to=${TO}`);
        const json = await res.json();

        new Chart(document.getElementById('bestSellersChart'), {
            type: 'bar',
            data: {
                labels  : json.labels,
                datasets: [{
                    label          : 'Units Sold',
                    data           : json.data,
                    backgroundColor: forestGreen,
                    borderRadius   : 6,
                }],
            },
            options: {
                responsive: true,
                indexAxis : 'y',
                plugins   : { legend: { display: false } },
                scales    : {
                    x: { grid: { color: gridColor }, ticks: { color: fontColor }, beginAtZero: true },
                    y: { grid: { display: false },   ticks: { color: fontColor, font: { size: 11 } } },
                },
            },
        });
    } catch (e) { console.error('Best sellers chart:', e); }

    // ── 4. Orders by status donut ─────────────
    try {
        const res  = await fetch(`${BASE}/admin/api/status-chart`);
        const json = await res.json();

        const STATUS_COLORS = {
            pending   : '#f59e0b',
            confirmed : '#3b82f6',
            processing: '#8b5cf6',
            ready     : '#06b6d4',
            delivered : '#22c55e',
            cancelled : '#ef4444',
        };

        const colors = json.labels.map(l => STATUS_COLORS[l] || '#9ca3af');

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels  : json.labels,
                datasets: [{
                    data           : json.data,
                    backgroundColor: colors,
                    borderWidth    : 2,
                    borderColor    : '#fff',
                    hoverOffset    : 6,
                }],
            },
            options: {
                responsive : true,
                cutout     : '68%',
                plugins    : {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} orders`,
                        },
                    },
                },
            },
        });

        // Custom legend
        const legend = document.getElementById('statusLegend');
        json.labels.forEach((label, i) => {
            legend.innerHTML += `
                <div class="donut-legend-item">
                    <span class="donut-legend-dot" style="background:${colors[i]}"></span>
                    ${label} (${json.data[i]})
                </div>`;
        });
    } catch (e) { console.error('Status chart:', e); }

    // ── 5. Inventory bar chart ────────────────
    try {
        const res  = await fetch(`${BASE}/admin/api/inventory-chart`);
        const json = await res.json();

        new Chart(document.getElementById('inventoryChart'), {
            type: 'bar',
            data: {
                labels  : json.labels,
                datasets: [{
                    label          : 'Stock',
                    data           : json.data,
                    backgroundColor: json.colors,
                    borderRadius   : 4,
                }],
            },
            options: {
                responsive: true,
                plugins   : {
                    legend : { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} units` } },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: fontColor, font: { size: 11 }, maxRotation: 45 } },
                    y: { grid: { color: gridColor }, ticks: { color: fontColor }, beginAtZero: true },
                },
            },
        });
    } catch (e) { console.error('Inventory chart:', e); }

})();
</script>