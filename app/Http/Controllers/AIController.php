<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Company;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    /**
     * Get AI analysis for a specific incident
     */
    public function getIncidentAnalysis($incidentId)
    {
        try {
            $companyId = Auth::user()->company_id;
            $incident = Incident::where('id', $incidentId)
                ->where('company_id', $companyId)
                ->firstOrFail();
            
            $company = Company::find($companyId);
            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            $analysis = $aiService->generateIncidentResolutionSuggestions($incident, $company, $locale);
            return response()->json($analysis);
        } catch (\Exception $e) {
            Log::error('AI Incident Analysis failed: ' . $e->getMessage());
            return response()->json(['error' => 'AI analysis failed'], 500);
        }
    }

    /**
     * Refresh AI analysis for dashboard
     */
    public function refreshAIAnalysis()
    {
        try {
            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            $recentIncidents = Incident::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            if ($recentIncidents->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No incidents available for analysis'
                ], 400);
            }

            // Prepare additional statistical data for better AI analysis
            $additionalData = $this->prepareAdditionalStatisticalData($companyId);

            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            // Generate fresh AI analysis with enhanced data
            $aiAnalysis = $aiService->analyzeIncidents($recentIncidents, $company, $locale, $additionalData);
            $companyAnalysis = $aiService->analyzeCompanyProfile($company, $recentIncidents, $locale, $additionalData);
            $safetyRecommendations = $aiService->generateSafetyRecommendations($company, $recentIncidents, $locale, $additionalData);

            return response()->json([
                'success' => true,
                'message' => 'AI analysis refreshed successfully',
                'data' => [
                    'aiAnalysis' => $aiAnalysis,
                    'companyAnalysis' => $companyAnalysis,
                    'safetyRecommendations' => $safetyRecommendations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Analysis Refresh failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh AI analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare additional statistical data for AI analysis
     */
    private function prepareAdditionalStatisticalData($companyId, $dateFrom = null, $dateTo = null)
    {
        // Build base query with date filters
        $baseQuery = function($query) use ($companyId, $dateFrom, $dateTo) {
            $query->where('company_id', $companyId);
            
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        };

        // Get monthly incidents trend
        $monthlyIncidents = Incident::select(
                \DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                \DB::raw('COUNT(*) as count')
            )
            ->where($baseQuery)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get incidents by location (top 10)
        $incidentsByLocation = Incident::select('location', \DB::raw('COUNT(*) as count'))
            ->where($baseQuery)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get incidents by user (top 10)
        $incidentsByUser = Incident::with('user')
            ->where($baseQuery)
            ->select('user_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get incidents by severity
        $incidentsBySeverity = Incident::select('severity', \DB::raw('COUNT(*) as count'))
            ->where($baseQuery)
            ->groupBy('severity')
            ->get();

        // Get incidents by status
        $incidentsByStatus = Incident::select('status', \DB::raw('COUNT(*) as count'))
            ->where($baseQuery)
            ->groupBy('status')
            ->get();

        // Get incidents by day of week
        $incidentsByDayOfWeek = Incident::select(
                \DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                \DB::raw('COUNT(*) as count')
            )
            ->where($baseQuery)
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        // Get incidents by hour
        $incidentsByHour = Incident::select(
                \DB::raw('HOUR(created_at) as hour'),
                \DB::raw('COUNT(*) as count')
            )
            ->where($baseQuery)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $dataPeriod = 'All time';
        if ($dateFrom && $dateTo) {
            $dataPeriod = "From {$dateFrom} to {$dateTo}";
        } elseif ($dateFrom) {
            $dataPeriod = "From {$dateFrom}";
        } elseif ($dateTo) {
            $dataPeriod = "Until {$dateTo}";
        }

        return [
            'monthly_incidents' => $monthlyIncidents,
            'incidents_by_location' => $incidentsByLocation,
            'incidents_by_user' => $incidentsByUser,
            'incidents_by_severity' => $incidentsBySeverity,
            'incidents_by_status' => $incidentsByStatus,
            'incidents_by_day_of_week' => $incidentsByDayOfWeek,
            'incidents_by_hour' => $incidentsByHour,
            'analysis_date' => now()->toISOString(),
            'data_period' => $dataPeriod,
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ]
        ];
    }

    /**
     * Get AI analysis for company profile
     */
    public function getCompanyAnalysis()
    {
        try {
            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            $recentIncidents = Incident::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            if ($recentIncidents->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No incidents available for analysis'
                ], 400);
            }

            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            $companyAnalysis = $aiService->analyzeCompanyProfile($company, $recentIncidents, $locale);

            return response()->json([
                'success' => true,
                'data' => $companyAnalysis
            ]);

        } catch (\Exception $e) {
            Log::error('AI Company Analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze company profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get safety recommendations
     */
    public function getSafetyRecommendations()
    {
        try {
            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            $recentIncidents = Incident::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            if ($recentIncidents->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No incidents available for analysis'
                ], 400);
            }

            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            $safetyRecommendations = $aiService->generateSafetyRecommendations($company, $recentIncidents, $locale);

            return response()->json([
                'success' => true,
                'data' => $safetyRecommendations
            ]);

        } catch (\Exception $e) {
            Log::error('AI Safety Recommendations failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate safety recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get incident analysis
     */
    public function getIncidentAnalysisData()
    {
        try {
            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            $recentIncidents = Incident::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            if ($recentIncidents->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No incidents available for analysis'
                ], 400);
            }

            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            $aiAnalysis = $aiService->analyzeIncidents($recentIncidents, $company, $locale);

            return response()->json([
                'success' => true,
                'data' => $aiAnalysis
            ]);

        } catch (\Exception $e) {
            Log::error('AI Incident Analysis Data failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze incidents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive AI analysis (all types)
     */
    public function getComprehensiveAnalysis()
    {
        try {
            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            $recentIncidents = Incident::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            if ($recentIncidents->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No incidents available for analysis'
                ], 400);
            }

            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            // Generate all types of AI analysis
            $aiAnalysis = $aiService->analyzeIncidents($recentIncidents, $company, $locale);
            $companyAnalysis = $aiService->analyzeCompanyProfile($company, $recentIncidents, $locale);
            $safetyRecommendations = $aiService->generateSafetyRecommendations($company, $recentIncidents, $locale);

            return response()->json([
                'success' => true,
                'message' => 'Comprehensive AI analysis completed successfully',
                'data' => [
                    'aiAnalysis' => $aiAnalysis,
                    'companyAnalysis' => $companyAnalysis,
                    'safetyRecommendations' => $safetyRecommendations,
                    'analysisDate' => now()->toISOString(),
                    'incidentCount' => $recentIncidents->count(),
                    'companyName' => $company->name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Comprehensive Analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate comprehensive analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh AI analysis for statistics page with date filtering
     */
    public function refreshAIAnalysisWithDateFilter(Request $request)
    {
        try {
            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            
            // Get date filters from request
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            
            // Build query with date filters
            $query = Incident::where('company_id', $companyId);
            
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            
            $filteredIncidents = $query->orderBy('created_at', 'desc')
                ->limit(50) // Increased limit for statistics page
                ->get();

            if ($filteredIncidents->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No incidents available for analysis in the selected date range'
                ], 400);
            }

            // Prepare additional statistical data for better AI analysis
            $additionalData = $this->prepareAdditionalStatisticalData($companyId, $dateFrom, $dateTo);

            $aiService = new GeminiAIService();
            $locale = app()->getLocale();
            
            // Generate fresh AI analysis with enhanced data and date filtering
            $aiAnalysis = $aiService->analyzeIncidents($filteredIncidents, $company, $locale, $additionalData);
            $companyAnalysis = $aiService->analyzeCompanyProfile($company, $filteredIncidents, $locale, $additionalData);
            $safetyRecommendations = $aiService->generateSafetyRecommendations($company, $filteredIncidents, $locale, $additionalData);

            return response()->json([
                'success' => true,
                'message' => 'AI analysis refreshed successfully for selected date range',
                'data' => [
                    'aiAnalysis' => $aiAnalysis,
                    'companyAnalysis' => $companyAnalysis,
                    'safetyRecommendations' => $safetyRecommendations,
                    'dateRange' => [
                        'from' => $dateFrom,
                        'to' => $dateTo
                    ],
                    'incidentCount' => $filteredIncidents->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Analysis Refresh with Date Filter failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh AI analysis: ' . $e->getMessage()
            ], 500);
        }
    }
}
