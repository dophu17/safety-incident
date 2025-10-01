<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function dashboard()
    {
        // Only show data from the same company
        $companyId = Auth::user()->company_id;

        // Thống kê tổng quan - chỉ của công ty mình
        $totalIncidents = Incident::where('company_id', $companyId)->count();
        $totalUsers = User::where('company_id', $companyId)->count();
        $totalManagers = User::where('company_id', $companyId)->where('role', 'manager')->count();
        $totalEmployees = User::where('company_id', $companyId)->where('role', 'employee')->count();

        // Thống kê incidents theo tháng (6 tháng gần nhất)
        $monthlyIncidents = Incident::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Thống kê incidents theo ngày (30 ngày gần nhất)
        $dailyIncidents = Incident::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Thống kê incidents theo vị trí (top 10)
        $incidentsByLocation = Incident::select('location', DB::raw('COUNT(*) as count'))
            ->where('company_id', $companyId)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Thống kê incidents theo user (top 10)
        $incidentsByUser = Incident::with('user')
            ->where('company_id', $companyId)
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
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
            'incidentsByUser'
        ));
    }

    /**
     * Display users management page
     */
    public function users(Request $request)
    {
        // Only show users from the same company
        $companyId = Auth::user()->company_id;
        $query = User::where('company_id', $companyId)->with('company')->latest();

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

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user (employee)
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'company_id' => Auth::user()->company_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', __('Employee created successfully'));
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        // Check if user belongs to the same company
        if ($user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, User $user)
    {
        // Check if user belongs to the same company
        if ($user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:8'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('status', __('User updated successfully'));
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Check if user belongs to the same company
        if ($user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', __('You cannot delete yourself'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', __('User deleted successfully'));
    }

    /**
     * Display detailed statistics page
     */
    public function statistics(Request $request)
    {
        // Only show data from the same company
        $companyId = Auth::user()->company_id;
        
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Thống kê incidents theo khoảng thời gian - chỉ của công ty mình
        $incidentsInPeriod = Incident::where('company_id', $companyId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        // Thống kê theo ngày trong tuần
        $incidentsByDayOfWeek = Incident::select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        // Thống kê theo giờ trong ngày
        $incidentsByHour = Incident::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Thống kê theo tháng
        $incidentsByMonth = Incident::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Thống kê theo user
        $incidentsByUser = Incident::with('user')
            ->where('company_id', $companyId)
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
     * Display company information
     */
    public function company()
    {
        $user = Auth::user();
        $company = $user->company;

        // If manager doesn't have a company yet, create one
        if (!$company && $user->role === 'manager') {
            $company = Company::create([
                'name' => 'My Company',
                'size' => 'small',
            ]);
            
            $user->company_id = $company->id;
            $user->save();
        }

        return view('admin.company', compact('company'));
    }

    /**
     * Update company information
     */
    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->back()->with('error', __('Company not found'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'size' => ['required', 'in:small,medium,large,enterprise'],
            'employee_count' => ['nullable', 'integer', 'min:1'],
            'industry' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $company->update($validated);

        return redirect()->route('admin.company')->with('status', __('Company information updated successfully'));
    }
}
