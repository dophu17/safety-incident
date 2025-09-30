<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function dashboard()
    {
        // Thống kê tổng quan
        $totalIncidents = Incident::count();
        $totalUsers = User::count();
        $totalManagers = User::where('role', 'manager')->count();
        $totalEmployees = User::where('role', 'employee')->count();

        // Thống kê incidents theo tháng (6 tháng gần nhất)
        $monthlyIncidents = Incident::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Thống kê incidents theo ngày (30 ngày gần nhất)
        $dailyIncidents = Incident::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Thống kê incidents theo vị trí (top 10)
        $incidentsByLocation = Incident::select('location', DB::raw('COUNT(*) as count'))
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Thống kê incidents theo user (top 10)
        $incidentsByUser = Incident::with('user')
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Incidents gần đây nhất
        $recentIncidents = Incident::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalIncidents',
            'totalUsers',
            'totalManagers',
            'totalEmployees',
            'monthlyIncidents',
            'dailyIncidents',
            'incidentsByLocation',
            'incidentsByUser',
            'recentIncidents'
        ));
    }

    /**
     * Display incidents management page
     */
    public function incidents(Request $request)
    {
        $query = Incident::with('user')->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            // You can add status field to incidents table later
            // $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $incidents = $query->paginate(20);

        return view('admin.incidents', compact('incidents'));
    }

    /**
     * Display users management page
     */
    public function users(Request $request)
    {
        $query = User::latest();

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Display detailed statistics page
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Thống kê incidents theo khoảng thời gian
        $incidentsInPeriod = Incident::whereBetween('created_at', [$dateFrom, $dateTo])->count();

        // Thống kê theo ngày trong tuần
        $incidentsByDayOfWeek = Incident::select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        // Thống kê theo giờ trong ngày
        $incidentsByHour = Incident::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Thống kê theo tháng
        $incidentsByMonth = Incident::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Thống kê theo user
        $incidentsByUser = Incident::with('user')
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->get();

        return view('admin.statistics', compact(
            'dateFrom',
            'dateTo',
            'incidentsInPeriod',
            'incidentsByDayOfWeek',
            'incidentsByHour',
            'incidentsByMonth',
            'incidentsByUser'
        ));
    }

    /**
     * Export incidents data
     */
    public function exportIncidents(Request $request)
    {
        $query = Incident::with('user');

        // Apply same filters as incidents page
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $incidents = $query->get();

        $filename = 'incidents_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Title',
                'Content',
                'Location',
                'Occurred At',
                'User Name',
                'User Email',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->id,
                    $incident->title,
                    $incident->content,
                    $incident->location,
                    $incident->occurred_at,
                    $incident->user->name,
                    $incident->user->email,
                    $incident->created_at,
                    $incident->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
