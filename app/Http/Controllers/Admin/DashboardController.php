<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Payment, Enrollment, Membership, Plan, User, Course, Module, Lesson, Quiz};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats ringkas
        $stats = [
            'users'              => User::count(),
            'courses'            => Course::count(),
            'modules'            => Module::count(),
            'lessons'            => Lesson::count(),
            'quizzes'            => Quiz::count(),
            'plans'              => Plan::count(),
            'memberships_active' => Membership::where('status','active')->count(),
            'enrollments_active' => Enrollment::where('status','active')->count(),
            'payments_pending'   => Payment::where('status','pending')->count(),
            'revenue_month'      => (int) Payment::where('status','paid')
                                        ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                                        ->sum('amount'),
        ];

        // Recent lists
        $recentPayments = Payment::with(['user:id,name,email','plan:id,name','course:id,title'])
            ->orderByDesc(DB::raw('COALESCE(paid_at, created_at)'))
            ->take(5)->get();

        $recentEnrolls = Enrollment::with(['user:id,name,email','course:id,title'])
            ->latest('id')->take(5)->get();

        // ========= CHART DATA =========

        // Revenue bulanan (12 bulan terakhir)
        $monthStart = now()->startOfMonth()->subMonths(11);
        $monthEnd   = now()->startOfMonth();
        $monthPeriod = CarbonPeriod::create($monthStart, '1 month', $monthEnd);

        $revenueRaw = Payment::where('status','paid')
            ->whereBetween('paid_at', [$monthStart, now()])
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total','ym'); // ['2025-01' => 1000000, ...]

        $revenueMonthly = [
            'labels' => collect($monthPeriod)->map(fn($d) => Carbon::parse($d)->format('M Y'))->values(),
            'data'   => collect($monthPeriod)->map(function($d) use ($revenueRaw){
                            $ym = Carbon::parse($d)->format('Y-m');
                            return (int) ($revenueRaw[$ym] ?? 0);
                        })->values(),
        ];

        // Status pembayaran (count)
        $statusCounts = Payment::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c','status');
        $paymentsStatus = [
            'labels' => ['paid','pending','failed'],
            'data'   => [
                (int)($statusCounts['paid'] ?? 0),
                (int)($statusCounts['pending'] ?? 0),
                (int)($statusCounts['failed'] ?? 0),
            ],
        ];

        // Provider teratas (top 6 by count)
        $providersQ = Payment::whereNotNull('provider')
            ->select('provider', DB::raw('COUNT(*) as c'))
            ->groupBy('provider')
            ->orderByDesc('c')
            ->limit(6)
            ->get();
        $paymentsProviders = [
            'labels' => $providersQ->pluck('provider')->map(fn($p)=>strtoupper($p))->values(),
            'data'   => $providersQ->pluck('c')->map(fn($x)=>(int)$x)->values(),
        ];

        // Enrollment harian (14 hari)
        $dayStart = now()->startOfDay()->subDays(13);
        $dayEnd   = now()->startOfDay();
        $dayPeriod = CarbonPeriod::create($dayStart, '1 day', $dayEnd);

        // Pakai activated_at jika ada, fallback ke created_at
        $enrollRaw = Enrollment::where(function($q) use ($dayStart, $dayEnd){
                $q->whereBetween('activated_at', [$dayStart, $dayEnd->copy()->endOfDay()])
                  ->orWhere(function($qq) use ($dayStart, $dayEnd){
                      $qq->whereNull('activated_at')
                         ->whereBetween('created_at', [$dayStart, $dayEnd->copy()->endOfDay()]);
                  });
            })
            ->selectRaw('DATE(COALESCE(activated_at, created_at)) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c','d'); // ['2025-08-21' => 4, ...]

        $enrollmentsDaily = [
            'labels' => collect($dayPeriod)->map(fn($d)=>Carbon::parse($d)->format('d M'))->values(),
            'data'   => collect($dayPeriod)->map(function($d) use ($enrollRaw){
                            $key = Carbon::parse($d)->toDateString();
                            return (int)($enrollRaw[$key] ?? 0);
                        })->values(),
        ];

        // Active membership by plan
        $planCounts = Membership::where('status','active')
            ->select('plan_id', DB::raw('COUNT(*) as c'))
            ->groupBy('plan_id')
            ->pluck('c', 'plan_id'); // [plan_id => count]

        $planNames = Plan::whereIn('id', $planCounts->keys())->pluck('name','id');

        $membershipsByPlan = [
            'labels' => $planCounts->keys()->map(fn($id)=>$planNames[$id] ?? ('Plan #'.$id))->values(),
            'data'   => $planCounts->values()->map(fn($x)=>(int)$x)->values(),
        ];

        return view('admin.dashboard_admin.index', compact(
            'stats','recentPayments','recentEnrolls',
            'revenueMonthly','paymentsStatus','paymentsProviders','enrollmentsDaily','membershipsByPlan'
        ));
    }

    public function metrics(Request $r)
    {
        // metric bisa single "revenue" atau array ?metric[]=revenue&metric[]=providers
        $metrics = (array) ($r->input('metric') ?? []);
        if (empty($metrics)) {
            $metrics = [$r->input('m', 'revenue')]; // fallback alias ?m=revenue
        }

        $out = [];

        foreach ($metrics as $metric) {
            switch ($metric) {
                case 'revenue':
                    $months = max(1, min(24, (int) $r->input('months', 12)));
                    $out['revenue'] = $this->metricRevenueMonthly($months);
                    break;

                case 'payments_status':
                    $out['payments_status'] = $this->metricPaymentsStatus();
                    break;

                case 'providers':
                    $top = max(1, min(12, (int) $r->input('top', 6)));
                    $out['providers'] = $this->metricProvidersTop($top);
                    break;

                case 'enrollments':
                    $days = max(1, min(60, (int) $r->input('days', 14)));
                    $out['enrollments'] = $this->metricEnrollmentsDaily($days);
                    break;

                case 'memberships_plan':
                    $out['memberships_plan'] = $this->metricMembersPerPlan();
                    break;
            }
        }

        // Kalau cuma 1 metric, biar simple bisa return langsung objeknya
        if (count($out) === 1) {
            return response()->json(reset($out));
        }

        return response()->json($out);
    }

    public function charts()
    {
        // Halaman builder (UI pilih grafik)
        return view('admin.dashboard_admin.index');
    }

    // ===== Helpers =====

    protected function metricRevenueMonthly(int $months = 12): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);
        $end   = now()->endOfMonth();
        $period = CarbonPeriod::create($start, '1 month', $end);

        $raw = Payment::where('status','paid')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total','ym');

        $labels = [];
        $data   = [];
        foreach ($period as $p) {
            $ym = $p->format('Y-m');
            $labels[] = $p->format('M Y');
            $data[]   = (int) ($raw[$ym] ?? 0);
        }

        return [
            'label'  => 'Revenue',
            'labels' => $labels,
            'data'   => $data,
            // opsi default Chart.js
            'options' => [
                'scales' => [
                    'y' => ['beginAtZero' => true]
                ]
            ]
        ];
    }

    protected function metricPaymentsStatus(): array
    {
        $counts = Payment::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')->pluck('c','status');

        return [
            'labels' => ['paid','pending','failed'],
            'data'   => [
                (int)($counts['paid'] ?? 0),
                (int)($counts['pending'] ?? 0),
                (int)($counts['failed'] ?? 0),
            ],
        ];
    }

    protected function metricProvidersTop(int $top = 6): array
    {
        $rows = Payment::whereNotNull('provider')
            ->select('provider', DB::raw('COUNT(*) as c'))
            ->groupBy('provider')
            ->orderByDesc('c')
            ->limit($top)->get();

        return [
            'labels' => $rows->pluck('provider')->map(fn($p)=>strtoupper($p))->values(),
            'data'   => $rows->pluck('c')->map(fn($x)=>(int)$x)->values(),
        ];
    }

    protected function metricEnrollmentsDaily(int $days = 14): array
    {
        $start = now()->startOfDay()->subDays($days - 1);
        $end   = now()->endOfDay();
        $period = CarbonPeriod::create($start, '1 day', $end);

        $raw = Enrollment::whereBetween(DB::raw('COALESCE(activated_at, created_at)'), [$start, $end])
            ->selectRaw('DATE(COALESCE(activated_at, created_at)) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c','d');

        $labels = [];
        $data   = [];
        foreach ($period as $p) {
            $key = $p->toDateString();
            $labels[] = $p->format('d M');
            $data[]   = (int)($raw[$key] ?? 0);
        }

        return [
            'label'  => 'Enrollments',
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    protected function metricMembersPerPlan(): array
    {
        $counts = Membership::where('status','active')
            ->select('plan_id', DB::raw('COUNT(*) as c'))
            ->groupBy('plan_id')
            ->pluck('c','plan_id');

        $names = Plan::whereIn('id', $counts->keys())->pluck('name','id');

        return [
            'label'  => 'Active Members',
            'labels' => $counts->keys()->map(fn($id)=>$names[$id] ?? ('Plan #'.$id))->values(),
            'data'   => $counts->values()->map(fn($x)=>(int)$x)->values(),
        ];
    }
    
}
