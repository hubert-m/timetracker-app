<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="dashboardAnalytics()" x-init="fetchStats()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between">
                <div>
                    <h3 class="text-3xl font-bold text-white mb-2">Cześć, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-gray-400">Oto podsumowanie twojej pracy. Wybierz mądrze kolejne zadania.</p>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- KPI 1 -->
                <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-16 h-16 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-gray-400 text-sm font-medium mb-1">Czas (Ten miesiąc)</p>
                    <template x-if="isLoading">
                        <div class="h-10 w-24 bg-gray-700 rounded animate-pulse mt-2"></div>
                    </template>
                    <template x-if="!isLoading">
                        <h4 class="text-4xl font-extrabold text-white tracking-tight" x-text="stats.kpi.total_formatted_time"></h4>
                    </template>
                </div>

                <!-- KPI 2 -->
                <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-16 h-16 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <p class="text-gray-400 text-sm font-medium mb-1">Aktywne projekty</p>
                    <template x-if="isLoading">
                        <div class="h-10 w-16 bg-gray-700 rounded animate-pulse mt-2"></div>
                    </template>
                    <template x-if="!isLoading">
                        <h4 class="text-4xl font-extrabold text-white tracking-tight" x-text="stats.kpi.active_projects"></h4>
                    </template>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Bar Chart: Ostatnie 14 dni -->
                <div class="lg:col-span-2 bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 shadow-xl">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Zaraportowany czas (Ostatnie 14 dni)</h3>
                        <div class="px-3 py-1 text-xs font-semibold bg-indigo-500/10 text-indigo-400 rounded-full border border-indigo-500/20">Minuty</div>
                    </div>
                    
                    <div class="relative h-72 w-full">
                        <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center">
                            <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <canvas id="barChart" class="opacity-0 transition-opacity duration-1000" :class="{'opacity-100': !isLoading}"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart: Projekty -->
                <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 shadow-xl flex flex-col">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-white">Podział wg. projektów</h3>
                        <p class="text-xs text-gray-500 mt-1">Bieżący miesiąc (minuty)</p>
                    </div>
                    
                    <div class="relative flex-grow flex items-center justify-center min-h-[250px]">
                        <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center">
                            <svg class="animate-spin h-8 w-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <canvas id="pieChart" class="opacity-0 transition-opacity duration-1000" :class="{'opacity-100': !isLoading}"></canvas>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Moduł Logiki -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardAnalytics', () => ({
                isLoading: true,
                stats: {
                    kpi: { total_formatted_time: '00:00', active_projects: 0 },
                    bar_chart: [],
                    pie_chart: []
                },
                barChartInstance: null,
                pieChartInstance: null,

                async fetchStats() {
                    try {
                        const response = await fetch('{{ route('dashboard.stats') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            this.stats = await response.json();
                            this.renderCharts();
                        }
                    } catch (error) {
                        console.error("Błąd podczas pobierania statystyk:", error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                renderCharts() {
                    // Globalne ustawienia dla Chart.js dopasowane do ciemnego motywu
                    Chart.defaults.color = '#9ca3af';
                    Chart.defaults.font.family = 'Outfit, sans-serif';

                    // --- Wykres Słupkowy (Ostatnie 14 dni) ---
                    const barCtx = document.getElementById('barChart');
                    if (this.barChartInstance) this.barChartInstance.destroy();
                    
                    const barLabels = this.stats.bar_chart.map(item => item.label);
                    const barData = this.stats.bar_chart.map(item => item.total_minutes);
                    
                    // Definicja gradientu
                    let gradient = barCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)'); // indigo-500
                    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.1)');

                    this.barChartInstance = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: barLabels,
                            datasets: [{
                                label: 'Przepracowane minuty',
                                data: barData,
                                backgroundColor: gradient,
                                borderRadius: 6,
                                borderSkipped: false,
                                barThickness: 'flex',
                                maxBarThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    titleColor: '#fff',
                                    bodyColor: '#cbd5e1',
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            const mins = context.parsed.y;
                                            const h = Math.floor(mins / 60);
                                            const m = mins % 60;
                                            return `${h}h ${m}m (${mins} min)`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                                    ticks: { padding: 10 }
                                },
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { padding: 10 }
                                }
                            }
                        }
                    });

                    // --- Wykres Kołowy/Doughnut (Projekty z bieżącego miesiąca) ---
                    const pieCtx = document.getElementById('pieChart');
                    if (this.pieChartInstance) this.pieChartInstance.destroy();

                    const pieLabels = this.stats.pie_chart.length > 0 ? this.stats.pie_chart.map(item => item.label) : ['Brak danych'];
                    const pieData = this.stats.pie_chart.length > 0 ? this.stats.pie_chart.map(item => item.total_minutes) : [1];
                    const pieColors = this.stats.pie_chart.length > 0 ? this.stats.pie_chart.map(item => item.color || '#6366f1') : ['#374151'];

                    this.pieChartInstance = new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: pieLabels,
                            datasets: [{
                                data: pieData,
                                backgroundColor: pieColors,
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    padding: 12,
                                    callbacks: {
                                        label: function(context) {
                                            if (context.label === 'Brak danych') return 'Brak zapisów w tym miesiącu';
                                            const mins = context.parsed;
                                            const h = Math.floor(mins / 60);
                                            const m = mins % 60;
                                            return ` ${context.label}: ${h}h ${m}m`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }));
        });
    </script>
</x-app-layout>
