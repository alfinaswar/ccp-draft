@extends('layouts.app')

@section('content')
    @push('css')
        <style>
            #imsakiyah-floating-btn {
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 1050;
                width: 68px;
                height: 68px;
                background: #c6ceda;
                border-radius: 50%;
                box-shadow: 0 6px 18px rgba(51, 51, 51, .2);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                padding: 0;
                border: none;
                transition: box-shadow 0.2s;
            }

            #imsakiyah-floating-btn:hover {
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            }

            #imsakiyah-modal .modal-content {
                border-radius: 1rem;
            }
        </style>
    @endpush
    <div class="welcome d-lg-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center welcome-text">
            <h3 class="d-flex align-items-center">
                <img src="assets/img/icons/hi.svg" alt="img">&nbsp;
                Hai, {{ Auth::user()->name ?? 'User' }}!
            </h3>, &nbsp;
            <h6 id="random-quote" class="mt-1"></h6>


        </div>
        <div class="d-flex align-items-center">
            <h5 class="mb-0" id="tanggal-jam"></h5>
        </div>
    </div>
    <div class="row sales-cards">
        <div class="col-xl-6 col-sm-12 col-12">
            <div class="card d-flex align-items-center justify-content-between default-cover mb-4">
                <div>
                    <h6>Jumlah Permintaan Pembelian</h6>
                    <h3><span class="counters" data-count="{{ $TotalPermintaan }}">{{ $TotalPermintaan }} </span>
                        Permintaan
                    </h3>
                    <p class="sales-range"><span class="text-info"><i data-feather="info" class="feather-16"></i></span>
                        Keterangan: Jumlah total permintaan pembelian yang tercatat pada sistem hingga hari ini.</p>
                </div>
                <img src="{{ asset('assets/img/ccp/icon/monitor.png') }}" alt="img" style="width:90px; height:90px;">
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card color-info bg-primary mb-4 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h3 class="counters" data-count="{{ $TotalSelesai }}">{{ $TotalSelesai }}</h3>
                    <p>Jumlah Permintaan Selesai</p>
                </div>
                <img src="{{ asset('assets/img/ccp/icon/done.png') }}" alt="img" style="width:90px; height:90px;">
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card color-info bg-secondary mb-4 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h3 class="counters" data-count="{{ $TotalPermintaan - $TotalSelesai }}">
                        {{ $TotalPermintaan - $TotalSelesai }}</h3>
                    <p>Dalam Proses Pengajuan / Review</p>
                </div>
                <div>
                    <img src="{{ asset('assets/img/ccp/icon/reload.png') }}" alt="img"
                        style="width:90px; height:90px;">
                    <i data-feather="rotate-ccw" class="feather-16 ms-2" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Refresh"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h5 class="card-title">Permintaan</h5>
                </div> --}}
                <div class="card-body">
                    <div id="s-col-dummy" class="chart-set"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                {{-- <div class="card-header">
                    <h5 class="card-title">Permintaan</h5>
                </div> --}}
                <div class="card-body">
                    <div id="chart-avg-response-time" class="chart-set"></div>
                    {{-- <div>qwe</div> --}}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                {{-- <div class="card-header">
                    <h5 class="card-title">Permintaan</h5>
                </div> --}}
                <div class="card-body">
                    <div id="chart-avg-response-time-umum" class="chart-set"></div>
                    {{-- <div>qwe</div> --}}
                </div>
            </div>
        </div>
    </div>



    @include('users.alert-profile')
@endsection
@push('js')
    <script src="{{ asset('') }}assets/plugins/apexchart/apexcharts.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/apexchart/chart-data.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                series: [{
                        name: 'Medis',
                        data: {!! json_encode($permintaanMedis) !!}
                    },
                    {
                        name: 'Umum',
                        data: {!! json_encode($permintaanUmum) !!}
                    },
                    {
                        name: 'Proyek',
                        data: {!! json_encode($permintaanProyek) !!}
                    }
                ],
                xaxis: {
                    categories: {!! json_encode($bulanLabels) !!}
                },
                yaxis: {
                    tickAmount: 5,
                    stepSize: 5,
                    labels: {
                        formatter: function(val) {
                            return Math.round(val);
                        }
                    }
                },
                colors: ['#5A8DEE', '#39DA8A', '#F87272'],
                title: {
                    text: 'Permintaan per Jenis Pengajuan (Medis, Umum, Proyek) dalam 6 bulan terakhir',
                    align: 'center'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'top'
                }
            };

            var chart = new ApexCharts(document.querySelector("#s-col-dummy"), options);
            chart.render();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var avgResponseTime = {!! json_encode($avgResponseTime) !!};
            var jumlah = avgResponseTime.map(item => item.jumlah);
            var lebihDari2Minggu = avgResponseTime.map(item => item.lebih_dari_2minggu);
            var kurangDari2Minggu = avgResponseTime.map(item => item.kurang_dari_2minggu);
            var rataRataProses = avgResponseTime.map(item => item.rata_rata_proses);
            var bulan = avgResponseTime.map(item => item.bulan);

            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                series: [{
                        name: 'Selesai > 2 Minggu',
                        data: lebihDari2Minggu
                    },
                    {
                        name: 'Selesai ≤ 2 Minggu',
                        data: kurangDari2Minggu
                    }
                ],
                xaxis: {
                    categories: bulan
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Pengajuan'
                    },

                    labels: {
                        formatter: function(val) {
                            return Math.round(val);
                        }
                    }
                },
                colors: ['#F57224', '#28C76F'],
                title: {
                    text: 'Rangkuman Pengajuan & Proses Rata-rata 6 Bulan Terakhir (Medis)',
                    align: 'center'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        endingShape: 'rounded'
                    }
                },

                legend: {
                    position: 'top'
                },
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        let bulanText = bulan[dataPointIndex];
                        let proses = rataRataProses[dataPointIndex] ?
                            `<br/>Rata-rata proses: <b>${rataRataProses[dataPointIndex]}</b>` : "";
                        return `
                            <div style="padding:8px;">
                                <b>${bulanText}</b><br/>
                                Selesai &gt; 2 minggu: ${lebihDari2Minggu[dataPointIndex]}<br/>
                                Selesai ≤ 2 minggu: ${kurangDari2Minggu[dataPointIndex]}
                                ${proses}
                            </div>
                        `;
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart-avg-response-time"), options);
            chart.render();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var avgResponseTime = {!! json_encode($avgResponseTimeUmum) !!};
            var jumlah = avgResponseTime.map(item => item.jumlah);
            var lebihDari2Minggu = avgResponseTime.map(item => item.lebih_dari_2minggu);
            var kurangDari2Minggu = avgResponseTime.map(item => item.kurang_dari_2minggu);
            var rataRataProses = avgResponseTime.map(item => item.rata_rata_proses);
            var bulan = avgResponseTime.map(item => item.bulan);

            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                series: [{
                        name: 'Selesai > 2 Minggu',
                        data: lebihDari2Minggu
                    },
                    {
                        name: 'Selesai ≤ 2 Minggu',
                        data: kurangDari2Minggu
                    }
                ],
                xaxis: {
                    categories: bulan
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Pengajuan'
                    },

                    labels: {
                        formatter: function(val) {
                            return Math.round(val);
                        }
                    }
                },
                colors: ['#F57224', '#28C76F'],
                title: {
                    text: 'Rangkuman Pengajuan & Proses Rata-rata 6 Bulan Terakhir (Umum)',
                    align: 'center'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        endingShape: 'rounded'
                    }
                },

                legend: {
                    position: 'top'
                },
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        let bulanText = bulan[dataPointIndex];
                        let proses = rataRataProses[dataPointIndex] ?
                            `<br/>Rata-rata proses: <b>${rataRataProses[dataPointIndex]}</b>` : "";
                        return `
                            <div style="padding:8px;">
                                <b>${bulanText}</b><br/>
                                Selesai &gt; 2 minggu: ${lebihDari2Minggu[dataPointIndex]}<br/>
                                Selesai ≤ 2 minggu: ${kurangDari2Minggu[dataPointIndex]}
                                ${proses}
                            </div>
                        `;
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart-avg-response-time-umum"), options);
            chart.render();
        });
    </script>
    <script>
        // Quotes spesial edisi bulan puasa & Ramadhan!
        const quotes = [
            "Yuk, produktif hari ini! 💪",
            "Santai, tapi tetap berkarya ya 😎☕",
            "Ingat: Rejeki ngga ke mana, tapi deadline ke mana-mana 😜",
            "Gas pol! Jangan kasih kendor 🔥",
            "Kesalahan itu biasa, semangatnya yang luar biasa!",
            "Ngopi dulu biar otaknya encer ☕💡",
            "Tetap on, walau kadang pingin skip hari ini 🤣",
            "Work smart, bukan work overthinking ✨",
            "Healing boleh, produktif tetap on track 👌",
            "Kerja bagus, self-reward jangan lupa 🍦",
            "Semangat, sekecil apapun progresmu hari ini! 🙌",
            "Jangan lupa bahagia, biar kerja lancar ya 😁🎉",
            "Setiap hari adalah peluang baru untuk belajar 📚✨",
            "Pekerjaan berat terasa ringan kalau dikerjakan bareng 🤝",
            "Target hari ini = selesai satu tugas penting dulu!",
            "Break sebentar, lanjut produktif lagi yuk 🚀",
            "Jangan terburu-buru, hasil bagus datang dari proses 🍀",
            "Senyum dulu, biar urusan kerjaan ikut menyenangkan 😊",
            "Waktunya upgrade skill, take action sekarang! 🏆",
            "Ingat: Lebih baik selesai daripada sempurna tapi tertunda!",
            "Mulai hari dengan niat, akhiri dengan hasil. You got this! ✨",
            "Skill dan kopi, dua-duanya penting buat hari Senin ☕💻",
            "Tugas numpuk? Santai, ingat ada Shopee 7.7 😆",
            "Bekerja keras boleh, burnout jangan. Jaga mental health! 🧠🌈",
            "Multitasking kayak Avenger, tapi jangan lupa istirahat ya! 🦸‍♂️",
            "Habis zoom, rebahan sejenak. Recharge mode ON ⚡",
            "Masalah datang dan pergi, gaji tetap tanggal tua 😅",
            "Challenge accepted! Hari ini harus lebih baik dari kemarin 💯",
            "No drama, hanya solusi dan sedikit curhat di grup WA 🤭",
            "Swipe left masalah, swipe right peluang! 🔀",
            "Sibuk itu berproses, jangan lupa nikmati progress 🛣️",
            "Goals bukan sekadar wishlist, yuk mulai dari langkah kecil 📝✨",
            "Work-life balance itu hak, bukan privilege! ⚖️",
            "Scroll TikTok dikit, terus balik kerja lagi ya 😏📱",
            "Kadang butuh meme biar semangat kerja bareng tim 😂",
            "Jangan takut gagal, tiap error itu step closer ke lulus probation 👨‍💼",
            "Geng kerja remote, co-working space dan kopi adalah lifestyle ☕🏢",
            "Ngejar target sambil denger playlist happy: vibes only! 🎧😇",
            "Bulan depan libur nasional lagi, semangat dulu yuk! 🗓️",
            "Mouse, keyboard, dan semangat: weapon pekerja digital ☑️",
            "Kerja keras bareng, rayakan hasil barengan juga 🥳🎂",
            "Deadline boleh mepet, attitude tetap on point 👌",
            "Tips: Jangan cuma buka email, buka juga bekal cemilan 😋",
            "Kalo capek, virtual meeting sambil nyemil aja. Pura-pura serius 😜",
            "Keluar dari zona nyaman, masuk ke zona upgrading 🚀",
            "Mentor bilang: 'Jangan kerja sendirian, teamwork itu kunci!' 🗝️",
            "Take your time, me time, kita tim yang saling dukung 🤗",
            "Jangan cuma mikirin kerjaan doang, hidup juga dinikmati! 🌻"
        ];
        document.addEventListener('DOMContentLoaded', function() {
            const randomText = quotes[Math.floor(Math.random() * quotes.length)];
            document.getElementById('random-quote').innerText = randomText;
        });
    </script>
    <script>
        function updateDateTime() {
            const bulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const hari = [
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
            ];
            const now = new Date();
            const namaHari = hari[now.getDay()];
            const tanggal = now.getDate();
            const namaBulan = bulan[now.getMonth()];
            const tahun = now.getFullYear();

            let jam = now.getHours();
            let menit = now.getMinutes();
            let detik = now.getSeconds();
            jam = jam < 10 ? '0' + jam : jam;
            menit = menit < 10 ? '0' + menit : menit;
            detik = detik < 10 ? '0' + detik : detik;

            const str = `${namaHari}, ${tanggal} ${namaBulan} ${tahun} - ${jam}:${menit}:${detik}`;
            document.getElementById('tanggal-jam').innerHTML = str;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
@endpush
