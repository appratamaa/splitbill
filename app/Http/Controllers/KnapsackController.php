<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KnapsackController extends Controller
{
    public function index()
    {
        return view('split_bill');
    }

    public function solve(Request $request)
    {
        // --- 1. DATA PREPARATION ---
        $items = $request->input('items', []);
        $members = $request->input('members', []);
        $vouchersInput = $request->input('vouchers', []);
        $taxPercent = (float) $request->input('tax', 0);
        $serviceInput = $request->input('service', 0);
        $serviceNominal = is_string($serviceInput) ? (float)str_replace('.', '', $serviceInput) : (float)$serviceInput;

        // Hitung Subtotal Belanja Murni
        $subtotal = 0;
        $itemsMap = [];

        foreach ($items as $item) {
            $cleanPrice = (float) str_replace('.', '', $item['price']);
            $rowTotal = $cleanPrice * $item['qty'];
            $subtotal += $rowTotal;
            $itemsMap[$item['id']] = ['price' => $cleanPrice, 'name' => $item['name']];
        }

        // --- 2. KNAPSACK PREPARATION (0/1) ---
        // Scale down untuk mengurangi beban memori PHP jika nominal besar
        $scale = 1000; 
        $W = floor($subtotal / $scale);
        
        $wt = []; $val = []; $codes = [];

        foreach ($vouchersInput as $v) {
            // Validasi: Kode ada & Qty > 0
            if(empty($v['code']) || (isset($v['qty']) && $v['qty'] <= 0)) continue;

            $minSpend = (float) str_replace('.', '', $v['min_spend']);
            $discRaw = $v['discount'];
            
            // Konversi % ke Nominal Rupiah
            $discAmount = 0;
            if (strpos($discRaw, '%') !== false) {
                $pct = (float) str_replace('%', '', $discRaw);
                $discAmount = $subtotal * ($pct / 100);
            } else {
                $discAmount = (float) str_replace('.', '', $discRaw);
            }

            // Validasi Dasar (Hanya masukkan jika syarat min belanja terpenuhi)
            if ($minSpend <= $subtotal && $minSpend >= 0) {
                 $wt[] = floor($minSpend / $scale); // Weight (Scaled)
                 $val[] = (int) $discAmount;        // Value
                 $codes[] = strtoupper($v['code']); 
            }
        }
        $n = count($val);

        // --- 3. EKSEKUSI ALGORITMA ---
        
        // A. Iteratif
        $startIter = microtime(true);
        list($maxDiscountIter, $usedIndices) = $this->knapsackIterative($W, $wt, $val, $n);
        $endIter = microtime(true);
        $timeIter = ($endIter - $startIter) * 1000;

        // Logika: Hanya gunakan 1 voucher terbaik (Modifikasi aturan bisnis umum)
        $bestVoucherCode = "-";
        $finalDiscount = 0;
        $bestVoucherVal = 0;
        $bestVoucherWt = 0;
        
        if (!empty($usedIndices)) {
            $maxSingleVal = -1;
            $bestIdx = -1;
            foreach($usedIndices as $idx) {
                if($val[$idx] > $maxSingleVal) {
                    $maxSingleVal = $val[$idx];
                    $bestIdx = $idx;
                }
            }
            if($bestIdx != -1) {
                $finalDiscount = $maxSingleVal;
                $bestVoucherCode = $codes[$bestIdx];
                $bestVoucherVal = $val[$bestIdx];
                $bestVoucherWt = $wt[$bestIdx] * $scale; // Kembalikan ke skala asli untuk display
            }
        }
        
        $finalDiscount = min($finalDiscount, $subtotal);

        // B. Rekursif (Benchmark)
        $this->memo = [];
        $startRec = microtime(true);
        $this->knapsackRecursive($W, $wt, $val, $n);
        $endRec = microtime(true);
        $timeRec = ($endRec - $startRec) * 1000;

        // --- 4. FINAL CALCULATION ---
        $taxAmount = $subtotal * ($taxPercent / 100);
        $grossTotal = $subtotal + $taxAmount + $serviceNominal; 
        $grandTotal = $grossTotal - $finalDiscount; 

        // --- 5. SPLIT DISTRIBUTION ---
        $memberResults = [];
        foreach ($members as $m) {
            $myBill = 0;
            $selections = $m['selections'] ?? [];
            foreach ($selections as $itemId => $qtyTaken) {
                if (isset($itemsMap[$itemId]) && $qtyTaken > 0) {
                    $myBill += ($itemsMap[$itemId]['price'] * $qtyTaken);
                }
            }
            
            if ($subtotal > 0) {
                $ratio = $myBill / $subtotal;
                $shareTax = $taxAmount * $ratio;
                $shareService = $serviceNominal * $ratio;
                $shareDiscount = $finalDiscount * $ratio;
                $finalPay = ($myBill + $shareTax + $shareService) - $shareDiscount;
                
                $memberResults[] = [
                    'name' => $m['name'],
                    'subtotal' => $myBill,
                    'tax_share' => $shareTax,
                    'service_share' => $shareService,
                    'final_pay' => $finalPay,
                    'saved' => $shareDiscount
                ];
            }
        }

        $chartData = $this->generateBenchmarkData();

        return response()->json([
            'status' => 'success',
            'summary' => [
                'subtotal' => $subtotal,
                'gross_total' => $grossTotal, 
                'grand_total' => $grandTotal,
                'discount' => $finalDiscount,
                'tax' => $taxAmount,
                'service' => $serviceNominal,
                'used_vouchers' => [$bestVoucherCode],
            ],
            'members' => $memberResults,
            'algorithm' => [
                'iterative_time' => number_format($timeIter, 4),
                'recursive_time' => number_format($timeRec, 4),
                'best_val' => $bestVoucherVal,
                'best_wt' => $bestVoucherWt
            ],
            'complexity' => [
                'n' => $n,      // Jumlah Item
                'w' => $W,      // Kapasitas (Scaled)
                'total_ops' => $n * $W // Total Operasi
            ],
            'chart' => $chartData
        ]);
    }

    // --- ALGORITMA CORE ---

    private function knapsackIterative($W, $wt, $val, $n)
    {
        // Init DP Table
        $K = array_fill(0, $n + 1, array_fill(0, $W + 1, 0));
        
        for ($i = 0; $i <= $n; $i++) {
            for ($w = 0; $w <= $W; $w++) {
                if ($i == 0 || $w == 0) $K[$i][$w] = 0;
                else if ($wt[$i - 1] <= $w) $K[$i][$w] = max($val[$i - 1] + $K[$i - 1][$w - $wt[$i - 1]], $K[$i - 1][$w]);
                else $K[$i][$w] = $K[$i - 1][$w];
            }
        }
        
        // Backtracking untuk mencari item yang dipilih
        $res = $K[$n][$W];
        $maxValue = $res;
        $w = $W;
        $selectedIndices = [];
        
        for ($i = $n; $i > 0 && $res > 0; $i--) {
            if ($res == $K[$i - 1][$w]) continue;
            else {
                $selectedIndices[] = $i - 1; 
                $res = $res - $val[$i - 1];
                $w = $w - $wt[$i - 1];
            }
        }
        return [$maxValue, $selectedIndices];
    }

    private $memo = [];
    private function knapsackRecursive($W, $wt, $val, $n)
    {
        if ($n == 0 || $W == 0) return 0;
        
        $key = $n . '-' . $W;
        if (isset($this->memo[$key])) return $this->memo[$key];
        
        if ($wt[$n - 1] > $W) 
            return $this->memo[$key] = $this->knapsackRecursive($W, $wt, $val, $n - 1);
        else 
            return $this->memo[$key] = max($val[$n - 1] + $this->knapsackRecursive($W - $wt[$n - 1], $wt, $val, $n - 1), $this->knapsackRecursive($W, $wt, $val, $n - 1));
    }

    private function generateBenchmarkData()
    {
        // Benchmark dengan N yang bertambah
        $sizes = [10, 50, 100, 200, 300, 400, 500];
        $iterativeTimes = [];
        $recursiveTimes = [];
        $W_bench = 100; // Kapasitas tetap untuk benchmark N
        
        foreach ($sizes as $n) {
            $wt = []; $val = [];
            // Generate Dummy Data
            for($i=0; $i<$n; $i++) { $wt[] = rand(1, 50); $val[] = rand(10, 100); }
            
            // Ukur Iteratif
            $t1 = microtime(true);
            $this->knapsackIterative($W_bench, $wt, $val, $n);
            $iterativeTimes[] = (microtime(true) - $t1) * 1000;
            
            // Ukur Rekursif
            $this->memo = [];
            $t1 = microtime(true);
            $this->knapsackRecursive($W_bench, $wt, $val, $n);
            $recursiveTimes[] = (microtime(true) - $t1) * 1000;
        }
        
        return ['labels' => $sizes, 'iterative' => $iterativeTimes, 'recursive' => $recursiveTimes];
    }
}