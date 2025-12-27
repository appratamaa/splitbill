<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimasi Diskon Split Bill: Studi Iteratif vs Rekursif - eksten</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%234F46E5'/%3E%3Ctext x='50%25' y='50%25' dy='.35em' text-anchor='middle' font-family='sans-serif' font-weight='800' font-size='32' fill='white'%3ESP%3C/text%3E%3C/svg%3E">

    <meta name="description"
        content="Aplikasi optimasi pembagian bill menggunakan Algoritma 0/1 Knapsack. Bandingkan efisiensi pendekatan Iteratif vs Rekursif secara real-time.">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://eksten.koyeb.app/">
    <meta property="og:title" content="Split Bill Optimizer - eksten">
    <meta property="og:description"
        content="Hitung diskon maksimal & bagi tagihan otomatis dengan algoritma Knapsack. Studi kasus Tugas Besar AKA.">
    <meta property="og:image"
        content="https://placehold.co/1200x630/4f46e5/ffffff?text=Split+Bill+Optimizer&font=poppins">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://eksten.koyeb.app/">
    <meta property="twitter:title" content="Split Bill Optimizer - eksten">
    <meta property="twitter:description"
        content="Analisis kompleksitas algoritma Knapsack (Iteratif vs Rekursif) pada kasus pembagian bill.">
    <meta property="twitter:image"
        content="https://placehold.co/1200x630/4f46e5/ffffff?text=Split+Bill+Optimizer&font=poppins">

    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/auto-animate/0.8.1/index.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
        }

        .font-numbers {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.5px;
        }

        /* Glass Panel */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Inputs */
        .input-clean {
            background: #fff;
            border: 1px solid #CBD5E1;
            color: #1E293B;
            transition: all 0.2s;
        }

        .input-clean:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* Voucher Logic */
        #voucher-list {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .voucher-card {
            transition: background-color 0.3s ease, border-color 0.3s ease;
            border-left: 4px solid transparent;
        }

        .voucher-card.valid-voucher {
            background-color: #F0FDF4;
            border-left-color: #10B981;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1);
        }

        .best-label {
            display: none;
        }

        .voucher-card.best-pick .best-label {
            display: inline-block;
        }

        /* Buttons & Loader */
        .btn-qty {
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.2s;
        }

        .btn-qty:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* GANTI CSS LOADER LAMA KE TEKS ANIMASI */
        .loading-text span {
            display: inline-block;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .loading-text span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .loading-text span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Receipt Styles */
        #receipt-wrapper {
            background-image: url('https://grainy-gradients.vercel.app/noise.svg');
            background-color: #ffffff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #receipt-scroll-container {
            max-height: 65vh;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #receipt-modal,
            #receipt-modal * {
                visibility: visible;
            }

            #receipt-modal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: white;
                z-index: 9999;
                display: flex !important;
                justify-content: center;
                align-items: flex-start;
            }

            #receipt-scroll-container {
                max-height: none;
                overflow: visible;
                box-shadow: none;
            }

            #receipt-wrapper {
                width: 58mm;
                margin: 0;
                border: none;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="min-h-screen pb-12 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]">

    <nav class="fixed top-0 w-full z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h1 class="font-bold text-xl leading-none text-slate-800">Split Bill</h1>
                    <span class="text-xs font-bold text-indigo-500 tracking-widest lowercase">eksten</span>
                </div>
            </div>
            <div
                class="hidden md:flex items-center gap-2 text-xs font-semibold bg-slate-100 px-4 py-2 rounded-full border border-slate-200 text-slate-600">
                <i class="fa-solid fa-code-branch"></i> Studi Iteratif vs Rekursif pada Algoritma 0/1 Knapsack
            </div>
            <button onclick="resetData()"
                class="text-xs font-bold text-slate-400 hover:text-red-500 transition uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>
    </nav>

    <main class="pt-28 px-4 sm:px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-7 space-y-6">

            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span
                            class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 text-white text-xs">1</span>
                        Produk & Harga
                    </h2>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Total Belanja (W)</p>
                        <p class="text-2xl font-bold text-indigo-600 font-numbers" id="temp-subtotal">Rp 0</p>
                    </div>
                </div>

                <div
                    class="-mx-6 md:mx-0 md:bg-white md:border md:border-slate-200 md:rounded-2xl md:overflow-hidden mb-4 transition-all">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[320px]">
                            <thead
                                class="bg-slate-50/50 md:bg-slate-50 text-[10px] md:text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                                <tr>
                                    <th class="pl-6 pr-2 md:px-4 py-3 text-left w-auto">Produk</th>
                                    <th class="px-1 md:px-4 py-3 text-center w-14 md:w-24">Qty</th>
                                    <th class="px-1 md:px-4 py-3 text-right w-28 md:w-40">Harga</th>
                                    <th class="pl-1 pr-6 md:px-4 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="product-list" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>

                    <button onclick="addProductRow()"
                        class="w-full py-3 text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition border-t border-slate-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> TAMBAH BARIS
                    </button>
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <h2 class="text-lg font-bold text-slate-800 mb-2 flex items-center gap-3">
                    <span
                        class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 text-white text-xs">2</span>
                    Anggota
                </h2>
                <p class="text-xs text-slate-400 mb-6 ml-10">Input anggota dan pilih item yang mereka pesan.</p>

                <div id="members-container" class="grid grid-cols-1 gap-4 mb-4"></div>

                <button onclick="addMember()"
                    class="w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-400 font-bold hover:text-indigo-600 hover:border-indigo-400 transition">
                    + Tambah Anggota
                </button>
            </div>

            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-3">
                    <span
                        class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 text-white text-xs">3</span>
                    Voucher & Pajak
                </h2>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pajak (%)</label>
                        <input type="number" id="tax-input"
                            class="input-clean w-full rounded-xl px-4 py-3 font-bold font-numbers" value="10"
                            oninput="saveLocal()">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Service (Rp)</label>
                        <input type="text" id="service-input"
                            class="input-clean w-full rounded-xl px-4 py-3 font-bold font-numbers" placeholder="0"
                            onkeyup="formatRupiah(this); saveLocal()">
                    </div>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold text-slate-700">Daftar Voucher (Item Knapsack)</h3>
                        <button onclick="openVoucherManager()"
                            class="text-xs bg-white border border-slate-300 px-3 py-1.5 rounded-lg font-bold text-indigo-600 hover:shadow-sm">
                            <i class="fa-solid fa-gear mr-1"></i> Kelola Voucher
                        </button>
                    </div>
                    <div id="voucher-list" class="space-y-3"></div>
                </div>
            </div>

            <button onclick="generateBill()"
                class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-lg py-5 rounded-2xl shadow-xl transition-all transform active:scale-[0.98] flex justify-center items-center gap-3 group">
                <span>Jalankan Algoritma</span>
                <i class="fa-solid fa-bolt group-hover:animate-pulse"></i>
            </button>
        </div>

        <div class="lg:col-span-5 space-y-6">

            <div id="results-area" class="hidden space-y-6 fade-in">

                <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white relative">
                        <div class="absolute right-0 top-0 p-6 opacity-10 text-8xl"><i
                                class="fa-solid fa-receipt"></i></div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Tagihan Final
                        </p>
                        <h2 class="text-5xl font-bold font-numbers mb-2 tracking-tight" id="res-grand-total">Rp 0</h2>

                        <div class="mb-4">
                            <p class="text-[10px] text-slate-400">Total Awal (Sub + Tax + Svc)</p>
                            <p class="text-sm font-bold font-numbers" id="res-gross-total">Rp 0</p>
                        </div>

                        <div class="border-t border-white/10 pt-4 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">Voucher Dipilih</p>
                                <p class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-tag text-emerald-400"></i>
                                    <span id="res-voucher-used">-</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-emerald-400 uppercase font-bold">Total Hemat</p>
                                <p class="text-lg font-bold font-numbers text-emerald-400" id="res-discount">Rp 0</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xs font-bold text-slate-400 uppercase">Detail Pembagian</h3>
                            <button onclick="showReceiptModal()"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                <i class="fa-solid fa-print"></i> Cetak Struk
                            </button>
                        </div>
                        <div id="split-results" class="space-y-3"></div>
                    </div>
                </div>

                <div class="glass-panel rounded-3xl p-6">
                    <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-indigo-600"></i> Detail Algoritma 0/1 Knapsack
                    </h3>
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="bg-white p-3 rounded-xl border border-slate-200 text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Big O (Worst)</p>
                            <p class="font-numbers font-bold text-sm text-slate-800">O(N·W)</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-200 text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Big Theta (Avg)</p>
                            <p class="font-numbers font-bold text-sm text-slate-800">Θ(N·W)</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-200 text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Big Omega (Best)</p>
                            <p class="font-numbers font-bold text-sm text-slate-800">Ω(N·W)</p>
                        </div>
                    </div>

                    <div
                        class="space-y-4 text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6 leading-relaxed">
                        <div>
                            <p class="font-bold text-indigo-600 uppercase mb-1">1. Definisi Masalah</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li><strong>Capacity (W):</strong> Total belanja pengguna (Subtotal).</li>
                                <li><strong>Items (N):</strong> Daftar voucher diskon.</li>
                                <li><strong>Weight (wt):</strong> Syarat minimum belanja voucher.</li>
                                <li><strong>Value (val):</strong> Nominal potongan harga.</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-bold text-indigo-600 uppercase mb-1">2. Relasi Rekurens (Dynamic
                                Programming)</p>
                            <p class="mb-1">Kami menggunakan pendekatan <em>Bottom-Up</em> untuk mengisi tabel
                                $K[i][w]$:</p>
                            <div
                                class="bg-white p-2 rounded border border-slate-200 font-numbers text-[10px] overflow-x-auto">
                                if (wt[i] <= w): <br>
                                    &nbsp;&nbsp; K[i][w] = max(val[i] + K[i-1][w-wt[i]], K[i-1][w]) <br>
                                    else: <br>
                                    &nbsp;&nbsp; K[i][w] = K[i-1][w]
                            </div>
                        </div>
                        <div>
                            <p class="font-bold text-indigo-600 uppercase mb-1">3. Analisis Efisiensi</p>
                            <p>
                                Algoritma berjalan dalam waktu <strong>Pseudo-Polynomial</strong>. Meskipun efisien
                                untuk N kecil, kompleksitas bergantung linear pada kapasitas W (Total Belanja). Kami
                                membandingkan pendekatan <strong>Iteratif</strong> (Tabulasi) dan
                                <strong>Rekursif</strong> (Memoization) di bawah ini.
                            </p>
                        </div>
                    </div>

                    <h4 class="font-bold text-xs text-slate-400 uppercase mb-3">Grafik Benchmark Runtime (Line Chart)
                    </h4>
                    <div class="h-48 w-full bg-white p-2 rounded-xl border border-slate-200 mb-2">
                        <canvas id="complexityChart"></canvas>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-slate-500 px-1">
                        <span class="text-indigo-600">Iteratif: <span id="time-iter"
                                class="font-numbers">0ms</span></span>
                        <span class="text-pink-600">Rekursif: <span id="time-rec"
                                class="font-numbers">0ms</span></span>
                    </div>
                </div>

            </div>

            <div id="empty-state"
                class="h-full min-h-[500px] glass-panel rounded-3xl flex flex-col items-center justify-center text-center p-10 border-2 border-dashed border-slate-300">
                <i class="fa-solid fa-calculator text-4xl text-indigo-200 mb-4"></i>
                <h3 class="font-bold text-lg text-slate-700">Siap Menghitung</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-xs">Masukkan produk, anggota, dan voucher untuk memulai
                    analisis algoritma.</p>
            </div>
        </div>
    </main>

    <div id="manage-voucher-modal"
        class="hidden fixed inset-0 z-[80] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl p-6 shadow-2xl relative">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2"><i
                    class="fa-solid fa-ticket text-indigo-600"></i> Kelola Daftar Voucher</h3>

            <div class="grid grid-cols-12 gap-2 text-[10px] font-bold text-slate-400 uppercase mb-2 px-2">
                <div class="col-span-3">Kode</div>
                <div class="col-span-3 text-right">Diskon (Rp/%)</div>
                <div class="col-span-3 text-right">Min Belanja</div>
                <div class="col-span-2 text-center">Qty</div>
                <div class="col-span-1"></div>
            </div>

            <div id="voucher-input-list" class="space-y-2 mb-4 max-h-[50vh] overflow-y-auto pr-1"></div>

            <button onclick="addVoucherRowModal()"
                class="w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-400 font-bold hover:text-indigo-600 hover:border-indigo-400 transition mb-4 text-sm">
                + Tambah Voucher Baru
            </button>

            <div class="flex justify-end">
                <button id="btn-save-voucher" onclick="closeVoucherManager()"
                    class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">
                    Selesai & Simpan
                </button>
            </div>
        </div>
    </div>

    <div id="loading"
        class="fixed inset-0 z-[100] bg-white/80 backdrop-blur-md flex flex-col justify-center items-center transition-opacity duration-500">
        <div class="flex items-center gap-3 mb-2 animate-pulse">
            <div
                class="bg-indigo-600 text-white w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h1 class="font-extrabold text-3xl text-slate-800 tracking-tight">eksten<span
                    class="text-indigo-600">.</span></h1>
        </div>
        <p id="loading-text" class="text-slate-500 text-xs font-bold uppercase tracking-[0.3em] mt-4">Memuat
            Split Bill...</p>
    </div>

    <div id="success-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="relative bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl scale-100 transition-transform">
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg shadow-emerald-50">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1">Berhasil!</h3>
                <p class="text-slate-500 text-xs mb-6">Kombinasi voucher terbaik ditemukan.</p>
                <div id="popup-vouchers" class="flex flex-wrap gap-2 justify-center mb-6"></div>
                <button onclick="closeModal()"
                    class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-slate-800 transition">Lihat
                    Hasil</button>
            </div>
        </div>
    </div>

    <div id="receipt-modal"
        class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-sm shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]"
            id="receipt-container">
            <div id="receipt-scroll-container">
                <div id="receipt-wrapper"
                    class="p-8 font-mono text-sm text-slate-900 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] bg-cover">
                    <div class="text-center mb-6 pb-4 border-b-2 border-dashed border-slate-400/50">
                        <h2 class="text-3xl font-extrabold lowercase tracking-tight mb-1 text-indigo-900">eksten</h2>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest">Official Receipt</p>
                        <p class="text-[10px] text-slate-400 mt-1" id="rec-date"></p>
                    </div>

                    <div id="rec-items"
                        class="space-y-2 mb-4 pb-4 border-b-2 border-dashed border-slate-400/50 text-xs"></div>

                    <div class="space-y-1 text-right mb-4 pb-4 border-b-2 border-dashed border-slate-400/50 text-xs">
                        <div class="flex justify-between"><span>Subtotal</span><span id="rec-sub">0</span></div>
                        <div class="flex justify-between"><span>Tax</span><span id="rec-tax-only">0</span></div>
                        <div class="flex justify-between"><span>Service</span><span id="rec-svc-only">0</span></div>
                        <div class="flex justify-between font-bold text-emerald-600"><span>Voucher <span
                                    id="rec-voucher-code"
                                    class="text-[9px] font-normal text-slate-500"></span></span><span
                                id="rec-disc">0</span></div>
                    </div>

                    <div class="flex justify-between items-center text-xl font-bold mb-6 text-slate-900">
                        <span>TOTAL</span>
                        <span id="rec-total">0</span>
                    </div>

                    <div class="text-center mb-3">
                        <div
                            class="inline-block bg-slate-900 text-white text-[9px] font-bold px-2 py-1 rounded uppercase tracking-wider">
                            Split Bill Details</div>
                    </div>
                    <div id="rec-splits" class="space-y-2 text-[10px]"></div>

                    <div class="mt-8 text-center">
                        <p class="text-[9px] text-slate-400">Generated by eksten web app</p>
                        <p class="text-[9px] text-slate-400">0/1 Knapsack Algorithm</p>
                        <i class="fa-solid fa-barcode text-4xl text-slate-800 mt-2 opacity-30"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 flex gap-2 border-t border-slate-100 no-print z-10">
                <button id="btn-download" onclick="downloadReceipt()"
                    class="flex-1 bg-indigo-600 text-white py-2 rounded-lg font-bold text-xs hover:bg-indigo-700 flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-download"></i> <span class="btn-text">Unduh Gambar</span>
                </button>
                <button id="btn-print" onclick="printReceipt()"
                    class="flex-1 bg-slate-800 text-white py-2 rounded-lg font-bold text-xs hover:bg-slate-700 flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-print"></i> <span class="btn-text">Cetak</span>
                </button>
                <button onclick="document.getElementById('receipt-modal').classList.add('hidden')"
                    class="w-10 bg-slate-100 text-slate-500 rounded-lg hover:bg-red-100 hover:text-red-500">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>
    <canvas id="confetti-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-[9999]"></canvas>

    <script>
        // --- GLOBAL STATE ---
        let globalItems = [];
        let membersData = [];
        let vouchers = [{
                code: "HEMAT10",
                discount: "10.000",
                min_spend: "50.000",
                qty: 5
            },
            {
                code: "DISKON50",
                discount: "50.000",
                min_spend: "200.000",
                qty: 2
            },
            {
                code: "NAT4L",
                discount: "15.000",
                min_spend: "5.000",
                qty: 10
            }
        ];
        let lastResult = null;

        // --- INITIAL LOADING ---
        document.addEventListener('DOMContentLoaded', () => {
            // PERBAIKAN: Loading Awal (Splash Screen)
            setTimeout(() => {
                const loader = document.getElementById('loading');
                loader.style.opacity = '0'; // Fade out
                setTimeout(() => {
                    loader.classList.add('hidden'); // Sembunyikan setelah fade selesai
                }, 500); // Sesuai durasi transition CSS (500ms)
            }, 1500); // Tahan loading selama 1.5 detik agar terlihat user

            loadLocal();
            if (document.getElementById('voucher-list')) {
                autoAnimate(document.getElementById('voucher-list'));
            }
            if (globalItems.length === 0) addProductRow();
            if (membersData.length === 0) addMember();
            syncGlobalItems();
        });

        // --- UTILS ---
        const formatRupiah = (el) => {
            let val = el.value.replace(/[^0-9]/g, '');
            if (val) el.value = parseInt(val).toLocaleString('id-ID').replace(/,/g, '.');
        }
        const formatDiscountInput = (el) => {
            if (el.value.includes('%')) return;
            formatRupiah(el);
        }
        const cleanRupiah = (str) => (!str) ? 0 : parseFloat(str.toString().replace(/\./g, '')) || 0;
        const fmtMoney = (n) => "Rp " + Math.round(n).toLocaleString('id-ID').replace(/,/g, '.');
        const formatNumberDots = (numStr) => {
            if (!numStr) return '0';
            if (numStr.includes('%')) return numStr;
            let clean = numStr.replace(/\./g, '');
            if (isNaN(clean)) return numStr;
            return parseInt(clean).toLocaleString('id-ID').replace(/,/g, '.');
        }

        // --- PRODUCTS ---
        function addProductRow(n = '', q = 1, p = '') {
            const tbody = document.getElementById('product-list');
            const id = 'item_' + Date.now() + Math.random().toString(36).substr(2, 5);
            const row = document.createElement('tr');
            row.dataset.id = id;

            row.innerHTML = `
                <td class="pl-6 pr-1 md:px-4 py-2 md:py-3 align-top">
                    <input type="text" value="${n}" class="input-clean w-full rounded-lg px-2 md:px-3 py-2 text-xs md:text-sm font-semibold text-slate-700 placeholder-slate-300 transition" placeholder="Item..." oninput="syncGlobalItems()">
                </td>
                <td class="px-1 md:px-4 py-2 md:py-3 align-top">
                    <input type="number" value="${q}" class="input-clean w-full rounded-lg px-1 py-2 text-xs md:text-sm text-center font-bold font-numbers text-slate-600" min="1" oninput="syncGlobalItems()">
                </td>
                <td class="px-1 md:px-4 py-2 md:py-3 align-top">
                    <div class="relative">
                        <span class="absolute left-2 md:left-3 top-2 text-slate-400 text-[10px] md:text-xs font-bold font-numbers">Rp</span>
                        <input type="text" value="${p}" class="input-clean w-full rounded-lg pl-7 md:pl-9 pr-2 md:pr-3 py-2 text-xs md:text-sm text-right font-bold font-numbers text-slate-700" placeholder="0" onkeyup="formatRupiah(this); syncGlobalItems()">
                    </div>
                </td>
                <td class="pl-1 pr-6 md:px-2 py-2 md:py-3 align-top text-center flex items-center justify-center h-full pt-3 md:pt-2">
                    <button onclick="this.closest('tr').remove(); syncGlobalItems()" class="text-slate-300 hover:text-red-500 transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            syncGlobalItems();
        }

        function syncGlobalItems() {
            globalItems = [];
            let total = 0;
            document.querySelectorAll('#product-list tr').forEach(row => {
                const pStr = row.querySelector('input[type="text"].text-right').value;
                const qty = parseInt(row.querySelector('input[type="number"]').value) || 0;
                const name = row.querySelector('input[type="text"]:first-child').value;
                globalItems.push({
                    id: row.dataset.id,
                    name: name,
                    qty: qty,
                    price: pStr
                });
                total += (cleanRupiah(pStr) * qty);
            });
            document.getElementById('temp-subtotal').innerText = fmtMoney(total);
            updateVoucherRanking(total);
            renderMembersUI();
            saveLocal();
        }

        function updateVoucherRanking(currentTotal) {
            let scoredVouchers = [];
            vouchers.forEach((v, idx) => {
                const qty = parseInt(v.qty) || 0;
                if (!v.code || !v.discount || !v.min_spend || qty <= 0) return;

                const min = cleanRupiah(v.min_spend);
                let val = 0;
                if (v.discount.includes('%')) {
                    val = currentTotal * (parseFloat(v.discount.replace('%', '')) / 100);
                } else {
                    val = cleanRupiah(v.discount);
                }

                if (currentTotal >= min && currentTotal > 0) {
                    scoredVouchers.push({
                        index: idx,
                        val: val
                    });
                }
            });

            scoredVouchers.sort((a, b) => b.val - a.val);
            renderVouchersMain(scoredVouchers);
        }

        function renderVouchersMain(scoredVouchers = []) {
            const list = document.getElementById('voucher-list');
            let rankMap = {};
            scoredVouchers.forEach((item, rank) => {
                rankMap[item.index] = rank;
            });
            const bestIndex = (scoredVouchers.length > 0) ? scoredVouchers[0].index : -1;

            vouchers.forEach((v, idx) => {
                const qty = parseInt(v.qty) || 0;
                const elId = `v-card-${idx}`;
                let el = document.getElementById(elId);

                if (qty <= 0) {
                    if (el) el.remove();
                    return;
                }

                const isBest = (idx === bestIndex);
                const isValid = rankMap.hasOwnProperty(idx);

                let orderStyle = isValid ? `order: ${-100 + rankMap[idx]};` : `order: 10;`;
                let classList =
                    `voucher-card bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between gap-2 relative`;
                if (isValid) classList += ' valid-voucher';
                if (isBest) classList += ' best-pick';

                const innerHTML = `
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-slate-800 uppercase">${v.code.toUpperCase()}</span>
                        <span class="text-[10px] text-slate-400">Min: ${formatNumberDots(v.min_spend)}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-emerald-600 font-numbers">${formatNumberDots(v.discount)}</span>
                        ${isBest ? '<div class="text-[9px] text-white bg-emerald-500 px-2 rounded font-bold inline-block ml-1">TERBAIK</div>' : ''}
                        <div class="text-[9px] text-slate-400 mt-0.5">Stok: ${qty}</div>
                    </div>
                `;

                if (!el) {
                    el = document.createElement('div');
                    el.id = elId;
                    el.className = classList;
                    el.style = orderStyle;
                    el.innerHTML = innerHTML;
                    list.appendChild(el);
                } else {
                    el.className = classList;
                    el.style = orderStyle;
                    if (el.innerHTML !== innerHTML) el.innerHTML = innerHTML;
                }
            });
        }

        // --- VOUCHER MANAGER ---
        function openVoucherManager() {
            renderVoucherManager();
            document.getElementById('manage-voucher-modal').classList.remove('hidden');
        }

        // MODIFIED: Close voucher dengan efek loading teks tombol
        function closeVoucherManager() {
            const btn = document.getElementById('btn-save-voucher');
            const originalText = btn.innerText;

            // Set loading state button
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Menyimpan...`;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            setTimeout(() => {
                document.getElementById('manage-voucher-modal').classList.add('hidden');
                syncGlobalItems();

                // Reset button
                btn.innerText = originalText;
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }, 600); // Fake delay
        }

        function renderVoucherManager() {
            const list = document.getElementById('voucher-input-list');
            list.innerHTML = '';
            vouchers.forEach((v, idx) => {
                const el = document.createElement('div');
                el.className = "bg-slate-50 p-3 rounded-xl border border-slate-200 relative mb-2";
                el.innerHTML = `
                    <div class="grid grid-cols-12 gap-2 w-full">
                        <div class="col-span-3">
                            <input type="text" value="${v.code}" class="w-full text-xs font-bold text-slate-800 uppercase outline-none bg-white border border-slate-200 rounded px-2 py-1 focus:border-indigo-500" oninput="this.value = this.value.toUpperCase(); vouchers[${idx}].code=this.value; saveLocal();">
                        </div>
                        <div class="col-span-3">
                            <input type="text" value="${v.discount}" class="w-full text-xs font-bold text-emerald-600 text-right outline-none bg-white border border-slate-200 rounded px-2 py-1 focus:border-emerald-500 font-numbers" onkeyup="formatDiscountInput(this); vouchers[${idx}].discount=this.value; saveLocal();">
                        </div>
                        <div class="col-span-3">
                            <input type="text" value="${v.min_spend}" class="w-full text-xs text-slate-600 text-right outline-none bg-white border border-slate-200 rounded px-2 py-1 focus:border-indigo-500 font-numbers" onkeyup="formatRupiah(this); vouchers[${idx}].min_spend=this.value; saveLocal();">
                        </div>
                        <div class="col-span-2">
                             <input type="number" value="${v.qty || 1}" class="w-full text-xs text-center font-bold text-slate-700 outline-none bg-white border border-slate-200 rounded px-1 py-1 focus:border-indigo-500 font-numbers" min="0" onchange="vouchers[${idx}].qty=this.value; saveLocal();">
                        </div>
                        <div class="col-span-1 flex items-center justify-end">
                             <button onclick="vouchers.splice(${idx},1); renderVoucherManager(); saveLocal();" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
        }

        function addVoucherRowModal() {
            vouchers.push({
                code: "",
                discount: "",
                min_spend: "",
                qty: 1
            });
            renderVoucherManager();
            saveLocal();
        }

        // --- MEMBERS ---
        function addMember(name = '') {
            membersData.push({
                id: 'mem_' + Date.now(),
                name: name,
                selections: {}
            });
            renderMembersUI();
            saveLocal();
        }

        function renderMembersUI() {
            const container = document.getElementById('members-container');
            container.innerHTML = '';
            membersData.forEach((mem, mIdx) => {
                const div = document.createElement('div');
                div.className = "bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative";
                div.innerHTML = `
                    <button onclick="membersData.splice(${mIdx},1); renderMembersUI(); saveLocal()" class="absolute top-4 right-4 text-slate-300 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                    <input type="text" value="${mem.name}" class="bg-transparent border-b border-slate-200 w-full px-2 py-1 text-sm font-bold text-slate-800 outline-none mb-3" placeholder="Nama..." oninput="membersData[${mIdx}].name=this.value; saveLocal()">
                    <div class="space-y-1 mt-2 member-list"></div>
                `;
                const list = div.querySelector('.member-list');
                if (globalItems.length === 0) list.innerHTML =
                    '<span class="text-xs text-slate-400 italic">Belum ada produk.</span>';

                globalItems.forEach(item => {
                    if (!item.name) return;
                    let taken = 0;
                    membersData.forEach((om, oi) => {
                        if (oi !== mIdx) taken += (om.selections[item.id] || 0);
                    });
                    const myQty = mem.selections[item.id] || 0;
                    const remain = item.qty - taken - myQty;
                    const canAdd = remain > 0;
                    const canSub = myQty > 0;

                    const row = document.createElement('div');
                    row.className =
                        `flex justify-between items-center p-2 rounded-lg text-xs ${myQty > 0 ? 'bg-indigo-50' : ''}`;
                    row.innerHTML = `
                        <div class="truncate w-3/5 font-medium text-slate-700">${item.name} <span class="text-slate-400 ml-1">@${item.price}</span></div>
                        <div class="flex items-center gap-2 bg-white rounded border border-slate-200 p-0.5">
                            <button class="btn-qty bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-500" onclick="updQty(${mIdx}, '${item.id}', -1)" ${!canSub?'disabled':''}>-</button>
                            <span class="font-numbers font-bold w-4 text-center">${myQty}</span>
                            <button class="btn-qty bg-indigo-50 hover:bg-indigo-100 text-indigo-600" onclick="updQty(${mIdx}, '${item.id}', 1)" ${!canAdd?'disabled':''}>+</button>
                        </div>
                    `;
                    list.appendChild(row);
                });
                container.appendChild(div);
            });
        }

        function updQty(mIdx, iId, d) {
            if (!membersData[mIdx].selections[iId]) membersData[mIdx].selections[iId] = 0;
            membersData[mIdx].selections[iId] += d;
            if (membersData[mIdx].selections[iId] <= 0) delete membersData[mIdx].selections[iId];
            renderMembersUI();
            saveLocal();
        }

        // --- SUBMIT ---
        // PERBAIKAN: Validasi Produk Wajib, tapi Anggota Opsional
        async function generateBill() {
            // 1. Validasi HANYA Produk
            if (globalItems.length === 0) {
                alert("Data Produk masih kosong. Silakan masukkan produk terlebih dahulu.");
                return;
            }

            // 2. Tampilkan Loading
            const loader = document.getElementById('loading');
            const loaderText = document.getElementById('loading-text');

            loaderText.innerText = "MENGANALISIS ALGORITMA...";
            loader.classList.remove('hidden');

            // Paksa browser me-render ulang
            void loader.offsetWidth;
            loader.style.opacity = '1';

            try {
                // --- TAMBAHAN: Jeda buatan selama 2 detik agar loading terlihat ---
                await new Promise(resolve => setTimeout(resolve, 2000));
                // ----------------------------------------------------------------

                const res = await fetch('/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: globalItems,
                        members: membersData,
                        vouchers: vouchers,
                        tax: document.getElementById('tax-input').value,
                        service: document.getElementById('service-input').value
                    })
                });

                if (!res.ok) throw new Error("Server Error");
                const data = await res.json();
                lastResult = data;

                renderResults(data);
                renderChart(data.chart);

                if (data.summary.used_vouchers.length > 0) {
                    const usedCode = data.summary.used_vouchers[0];
                    const vIdx = vouchers.findIndex(v => v.code.toUpperCase() === usedCode);
                    if (vIdx !== -1) {
                        vouchers[vIdx].qty = parseInt(vouchers[vIdx].qty) - 1;
                        if (vouchers[vIdx].qty < 0) vouchers[vIdx].qty = 0;
                        saveLocal();
                    }
                }

                const currentTotal = data.summary.subtotal;
                updateVoucherRanking(currentTotal);

                document.getElementById('empty-state').classList.add('hidden');
                document.getElementById('results-area').classList.remove('hidden');

                // Panggil Confetti setelah loading selesai
                setTimeout(() => {
                    fireConfetti();
                    showModal(data.summary.used_vouchers);
                }, 300);

            } catch (e) {
                alert("Error: " + e.message);
            } finally {
                // Sembunyikan Loading
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.classList.add('hidden');
                }, 500);
            }
        }

        function renderResults(data) {
            document.getElementById('res-grand-total').innerText = fmtMoney(data.summary.grand_total);
            document.getElementById('res-gross-total').innerText = fmtMoney(data.summary.gross_total);
            document.getElementById('res-discount').innerText = "- " + fmtMoney(data.summary.discount);
            let usedCode = data.summary.used_vouchers.length > 0 ? data.summary.used_vouchers[0] : "-";
            document.getElementById('res-voucher-used').innerText = usedCode.toUpperCase();

            const box = document.getElementById('split-results');
            box.innerHTML = '';
            data.members.forEach(m => {
                box.insertAdjacentHTML('beforeend', `
                    <div class="border-b border-slate-100 pb-3 last:border-0 hover:bg-slate-50 transition p-2 rounded">
                        <div class="flex justify-between items-center mb-1">
                            <p class="text-sm font-bold text-slate-800">${m.name}</p>
                            <p class="text-sm font-black text-indigo-600 font-numbers">${fmtMoney(m.final_pay)}</p>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 font-numbers">
                             <span>Sub: ${fmtMoney(m.subtotal)} | Tax: ${fmtMoney(m.tax_share)} | Svc: ${fmtMoney(m.service_share)}</span>
                             <span class="text-emerald-500 font-bold">Hemat ${fmtMoney(m.saved)}</span>
                        </div>
                    </div>
                `);
            });
            document.getElementById('time-iter').innerText = data.algorithm.iterative_time + "ms";
            document.getElementById('time-rec').innerText = data.algorithm.recursive_time + "ms";
        }

        function showReceiptModal() {
            if (!lastResult) return;
            const d = new Date();
            document.getElementById('rec-date').innerText = d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString();
            const ib = document.getElementById('rec-items');
            ib.innerHTML = '';
            globalItems.forEach(i => {
                ib.innerHTML +=
                    `<div class="flex justify-between"><span>${i.name} x${i.qty}</span><span>${i.price}</span></div>`;
            });
            document.getElementById('rec-sub').innerText = fmtMoney(lastResult.summary.subtotal);
            document.getElementById('rec-tax-only').innerText = fmtMoney(lastResult.summary.tax);
            document.getElementById('rec-svc-only').innerText = fmtMoney(lastResult.summary.service);
            const vCode = (lastResult.summary.used_vouchers && lastResult.summary.used_vouchers.length > 0) ? lastResult
                .summary.used_vouchers[0] : "";
            document.getElementById('rec-voucher-code').innerText = vCode ? `(${vCode.toUpperCase()})` : "";
            document.getElementById('rec-disc').innerText = "- " + fmtMoney(lastResult.summary.discount);
            document.getElementById('rec-total').innerText = fmtMoney(lastResult.summary.grand_total);
            const sb = document.getElementById('rec-splits');
            sb.innerHTML = '';
            lastResult.members.forEach(m => {
                sb.innerHTML += `
                <div class="border-t border-dashed border-slate-300 pt-1 mt-1">
                    <div class="flex justify-between font-bold"><span>${m.name}</span><span>${fmtMoney(m.final_pay)}</span></div>
                    <div class="text-[9px] text-slate-500 flex justify-between">
                        <span>Sub:${fmtMoney(m.subtotal)} | Tax:${fmtMoney(m.tax_share)} | Svc:${fmtMoney(m.service_share)}</span>
                        <span>Disc: -${fmtMoney(m.saved)}</span>
                    </div>
                </div>`;
            });
            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        // MODIFIED: Fungsi Download dengan Loading Button
        function downloadReceipt() {
            const btn = document.getElementById('btn-download');
            const btnText = btn.querySelector('.btn-text');
            const icon = btn.querySelector('i');
            const originalText = btnText.innerText;
            const originalIcon = icon.className;

            // State Loading
            btnText.innerText = "Mengunduh...";
            icon.className = "fa-solid fa-circle-notch fa-spin";
            btn.disabled = true;
            btn.classList.add('opacity-75');

            const el = document.getElementById('receipt-wrapper');
            html2canvas(el, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: null
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'struk-eksten.jpg';
                link.href = canvas.toDataURL('image/jpeg', 0.9);
                link.click();

                // Reset State
                setTimeout(() => {
                    btnText.innerText = originalText;
                    icon.className = originalIcon;
                    btn.disabled = false;
                    btn.classList.remove('opacity-75');
                }, 500);
            });
        }

        // ADDED: Fungsi Print dengan Loading Button
        function printReceipt() {
            const btn = document.getElementById('btn-print');
            const btnText = btn.querySelector('.btn-text');
            const icon = btn.querySelector('i');
            const originalText = btnText.innerText;
            const originalIcon = icon.className;

            btnText.innerText = "Memuat...";
            icon.className = "fa-solid fa-circle-notch fa-spin";

            setTimeout(() => {
                window.print();
                btnText.innerText = originalText;
                icon.className = originalIcon;
            }, 500);
        }

        function saveLocal() {
            const d = {
                globalItems,
                membersData,
                vouchers,
                tax: document.getElementById('tax-input').value,
                svc: document.getElementById('service-input').value
            };
            localStorage.setItem('eksten_data_final_fix_v3', JSON.stringify(d));
            const total = cleanRupiah(document.getElementById('temp-subtotal').innerText);
            updateVoucherRanking(total);
        }

        function loadLocal() {
            const raw = localStorage.getItem('eksten_data_final_fix_v3');
            if (raw) {
                const d = JSON.parse(raw);
                if (d.vouchers) vouchers = d.vouchers;
                if (d.tax) document.getElementById('tax-input').value = d.tax;
                if (d.svc) document.getElementById('service-input').value = d.svc;
                if (d.globalItems && d.globalItems.length > 0) {
                    const tbody = document.getElementById('product-list');
                    tbody.innerHTML = '';
                    d.globalItems.forEach(item => {
                        addProductRow(item.name, item.qty, item.price);
                    });
                }
                if (d.membersData && d.membersData.length > 0) {
                    membersData = d.membersData;
                    renderMembersUI();
                }
            }
        }

        // PERBAIKAN: Reset tanpa reload halaman agar Voucher/Tax/Service tidak hilang
        function resetData() {
            if (confirm("Hapus Data Produk & Anggota saja? (Voucher & Pajak tetap tersimpan)")) {
                // 1. Kosongkan Data Produk & Member
                globalItems = [];
                membersData = [];

                // 2. Bersihkan Tampilan HTML
                document.getElementById('product-list').innerHTML = '';
                document.getElementById('members-container').innerHTML = '';
                document.getElementById('temp-subtotal').innerText = 'Rp 0';

                // 3. Sembunyikan Area Hasil
                document.getElementById('results-area').classList.add('hidden');
                document.getElementById('empty-state').classList.remove('hidden');

                // 4. Tambahkan kembali 1 baris kosong default (UX)
                addProductRow();
                addMember();

                // 5. Update Ranking Voucher (kembali ke 0)
                updateVoucherRanking(0);

                // 6. Simpan state baru ke LocalStorage
                // Karena variabel 'vouchers', dan value input tax/service tidak kita ubah,
                // maka saat saveLocal dipanggil, data tersebut ikut tersimpan kembali dengan aman.
                saveLocal();

                // CATATAN: location.reload() DIHAPUS agar UX lebih mulus dan data lain tidak hilang.
            }
        }

        function fireConfetti() {
            // 1. Ambil elemen canvas manual yang kita buat
            const canvas = document.getElementById('confetti-canvas');

            // 2. Inisialisasi confetti khusus di canvas tersebut
            // 'resize: true' penting agar di HP menyesuaikan ukuran layar otomatis
            const myConfetti = confetti.create(canvas, {
                resize: true,
                useWorker: true
            });

            // 3. Tentukan posisi (Mobile butuh posisi sedikit lebih tinggi karena keyboard/nav)
            const isMobile = window.innerWidth < 768;
            const yOrigin = isMobile ? 0.5 : 0.6; // Mobile tengah layar, Desktop agak bawah

            // 4. Tembakkan!
            myConfetti({
                particleCount: 150,
                spread: isMobile ? 60 : 100, // Spread lebih kecil di HP biar padat
                origin: {
                    y: yOrigin
                },
                zIndex: 9999,
                ticks: 300, // Durasi lebih lama
                gravity: 1.2,
                scalar: isMobile ? 1.0 : 1.2, // Ukuran partikel
                shapes: ['circle', 'square'],
                colors: ['#4F46E5', '#10B981', '#F59E0B', '#EC4899']
            });
        }

        function showModal(used) {
            const c = document.getElementById('popup-vouchers');
            c.innerHTML = '';
            if (used.length > 0) used.forEach(code => c.innerHTML +=
                `<span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm font-bold font-numbers border border-emerald-200">${code.toUpperCase()}</span>`
            );
            else c.innerHTML = `<span class="text-xs text-slate-400 italic">Tidak ada voucher.</span>`;
            document.getElementById('success-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('success-modal').classList.add('hidden');
        }

        let myChart = null;

        function renderChart(d) {
            const ctx = document.getElementById('complexityChart').getContext('2d');
            if (myChart) myChart.destroy();
            const totalDuration = 2000;
            const delayBetweenPoints = totalDuration / d.labels.length;
            const previousY = (ctx) => ctx.index === 0 ? ctx.chart.scales.y.getPixelForValue(100) : ctx.chart
                .getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;
            const animation = {
                x: {
                    type: 'number',
                    easing: 'linear',
                    duration: delayBetweenPoints,
                    from: NaN,
                    delay(ctx) {
                        return ctx.type !== 'data' || ctx.xStarted ? 0 : ctx.index * delayBetweenPoints;
                    },
                    fn(ctx) {
                        if (ctx.type === 'data') ctx.xStarted = true;
                    }
                },
                y: {
                    type: 'number',
                    easing: 'linear',
                    duration: delayBetweenPoints,
                    from: previousY,
                    delay(ctx) {
                        return ctx.type !== 'data' || ctx.yStarted ? 0 : ctx.index * delayBetweenPoints;
                    },
                    fn(ctx) {
                        if (ctx.type === 'data') ctx.yStarted = true;
                    }
                }
            };
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: d.labels.map(l => 'N=' + l),
                    datasets: [{
                        label: 'Iteratif',
                        data: d.iterative,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4
                    }, {
                        label: 'Rekursif',
                        data: d.recursive,
                        borderColor: '#EC4899',
                        borderDash: [5, 5],
                        tension: 0.3,
                        pointRadius: 4
                    }]
                },
                options: {
                    animation,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'JetBrains Mono'
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: '#F1F5F9'
                            },
                            ticks: {
                                font: {
                                    family: 'JetBrains Mono'
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
    <footer class="mt-12 pb-4 text-center">
        <div
            class="inline-flex items-center gap-2 px-6 py-3 bg-white/50 backdrop-blur-sm rounded-full border border-slate-200 shadow-sm transition-all hover:shadow-md">
            <span class="text-xs font-medium text-slate-500 tracking-wide">
                © 2025 <span class="font-bold text-indigo-600">eksten</span>. Crafted with
            </span>
            <i class="fa-solid fa-heart text-pink-500 animate-pulse text-[10px]"></i>
            <span class="text-xs font-medium text-slate-500 tracking-wide">
                for AKA Final Project
            </span>
        </div>
    </footer>
</body>

</html>
