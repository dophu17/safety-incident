<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.0-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Analyze incidents and provide insights
     */
    public function analyzeIncidents($incidents, $company, $locale = 'vn', $additionalData = [])
    {
        try {
            $incidentData = $this->prepareIncidentData($incidents);
            $companyData = $this->prepareCompanyData($company);
            $statisticalData = $this->prepareStatisticalData($incidents, $additionalData);
            
            $prompt = $this->buildIncidentAnalysisPrompt($incidentData, $companyData, $statisticalData, $locale);
            
            $response = $this->makeRequest($prompt);
            
            return $this->parseIncidentAnalysisResponse($response, $locale);
        } catch (\Exception $e) {
            // Only log if it's not a 404 API error (which is expected when API key doesn't have access)
            if (strpos($e->getMessage(), '404') === false) {
                Log::error('Gemini AI Incident Analysis Error: ' . $e->getMessage());
            }
            
            // Return enhanced default analysis based on actual data
            return $this->getEnhancedDefaultIncidentAnalysis($incidents, $company, $locale);
        }
    }

    /**
     * Analyze company profile and provide recommendations
     */
    public function analyzeCompanyProfile($company, $incidents, $locale = 'vn', $additionalData = [])
    {
        try {
            $companyData = $this->prepareCompanyData($company);
            $incidentData = $this->prepareIncidentData($incidents);
            $statisticalData = $this->prepareStatisticalData($incidents, $additionalData);
            
            $prompt = $this->buildCompanyAnalysisPrompt($companyData, $incidentData, $statisticalData, $locale);
            
            $response = $this->makeRequest($prompt);
            
            return $this->parseCompanyAnalysisResponse($response, $locale);
        } catch (\Exception $e) {
            // Only log if it's not a 404 API error (which is expected when API key doesn't have access)
            if (strpos($e->getMessage(), '404') === false) {
                Log::error('Gemini AI Company Analysis Error: ' . $e->getMessage());
            }
            
            // Return enhanced default analysis based on actual company data
            return $this->getEnhancedDefaultCompanyAnalysis($company, $incidents, $locale);
        }
    }

    /**
     * Generate safety recommendations and warnings
     */
    public function generateSafetyRecommendations($company, $incidents, $locale = 'vn')
    {
        try {
            $companyData = $this->prepareCompanyData($company);
            $incidentData = $this->prepareIncidentData($incidents);
            
            $prompt = $this->buildSafetyRecommendationsPrompt($companyData, $incidentData, $locale);
            
            $response = $this->makeRequest($prompt);
            
            return $this->parseSafetyRecommendationsResponse($response, $locale);
        } catch (\Exception $e) {
            // Only log if it's not a 404 API error (which is expected when API key doesn't have access)
            if (strpos($e->getMessage(), '404') === false) {
                Log::error('Gemini AI Safety Recommendations Error: ' . $e->getMessage());
            }
            
            // Return enhanced default analysis based on actual data
            return $this->getEnhancedDefaultSafetyRecommendations($company, $incidents, $locale);
        }
    }

    /**
     * Generate incident resolution suggestions
     */
    public function generateIncidentResolutionSuggestions($incident, $company, $locale = 'vn')
    {
        try {
            $incidentData = $this->prepareSingleIncidentData($incident);
            $companyData = $this->prepareCompanyData($company);
            
            $prompt = $this->buildIncidentResolutionPrompt($incidentData, $companyData, $locale);
            
            $response = $this->makeRequest($prompt);
            
            return $this->parseIncidentResolutionResponse($response, $locale);
        } catch (\Exception $e) {
            Log::error('Gemini AI Incident Resolution Error: ' . $e->getMessage());
            return $this->getDefaultIncidentResolution($locale);
        }
    }

    private function makeRequest($prompt)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 4096,
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Gemini API request failed: ' . $response->body());
    }

    private function cleanJsonResponse($content)
    {
        // Remove common loading messages and non-JSON text
        $loadingMessages = [
            'Đang phân tích dữ liệu sự cố. Vui lòng chờ kết quả phân tích chi tiết.',
            'Đang thực hiện phân tích sự cố theo vị trí.',
            'Analyzing incident data. Please wait for detailed analysis results.',
            'Performing incident analysis by location.',
            'データを分析中です。詳細な分析結果をお待ちください。',
            '場所別のインシデント分析を実行中です。'
        ];
        
        foreach ($loadingMessages as $message) {
            $content = str_replace($message, '', $content);
        }
        
        // Remove any text before the first {
        $jsonStart = strpos($content, '{');
        if ($jsonStart !== false) {
            $content = substr($content, $jsonStart);
        }
        
        // Remove any text after the last }
        $jsonEnd = strrpos($content, '}');
        if ($jsonEnd !== false) {
            $content = substr($content, 0, $jsonEnd + 1);
        }
        
        // Clean up any extra whitespace
        $content = trim($content);
        
        return $content;
    }

    private function prepareIncidentData($incidents)
    {
        return $incidents->map(function ($incident) {
            return [
                'title' => $incident->title,
                'content' => $incident->content,
                'location' => $incident->location,
                'severity' => $incident->severity,
                'status' => $incident->status,
                'occurred_at' => $incident->occurred_at,
                'created_at' => $incident->created_at,
            ];
        })->toArray();
    }

    private function prepareSingleIncidentData($incident)
    {
        return [
            'title' => $incident->title,
            'content' => $incident->content,
            'location' => $incident->location,
            'severity' => $incident->severity,
            'status' => $incident->status,
            'occurred_at' => $incident->occurred_at,
        ];
    }

    private function prepareCompanyData($company)
    {
        return [
            'name' => $company->name,
            'industry' => $company->industry,
            'size' => $company->size,
            'employee_count' => $company->employee_count,
            'description' => $company->description,
            'address' => $company->address,
        ];
    }

    private function prepareStatisticalData($incidents, $additionalData = [])
    {
        $stats = [
            'total_incidents' => $incidents->count(),
            'incidents_by_severity' => $incidents->groupBy('severity')->map->count(),
            'incidents_by_status' => $incidents->groupBy('status')->map->count(),
            'incidents_by_location' => $incidents->groupBy('location')->map->count(),
            'incidents_by_month' => $incidents->groupBy(function($incident) {
                return $incident->created_at->format('Y-m');
            })->map->count(),
            'recent_incidents' => $incidents->take(5)->map(function($incident) {
                return [
                    'title' => $incident->title,
                    'content' => $incident->content,
                    'severity' => $incident->severity,
                    'location' => $incident->location,
                    'created_at' => $incident->created_at->format('Y-m-d H:i'),
                ];
            })->values(),
            'content_analysis' => $this->analyzeIncidentContent($incidents),
        ];

        // Add additional statistical data if provided
        if (isset($additionalData['monthly_incidents'])) {
            $stats['monthly_trend'] = $additionalData['monthly_incidents'];
        }
        if (isset($additionalData['incidents_by_location'])) {
            $stats['location_analysis'] = $additionalData['incidents_by_location'];
        }
        if (isset($additionalData['incidents_by_user'])) {
            $stats['user_analysis'] = $additionalData['incidents_by_user'];
        }

        return $stats;
    }

    private function analyzeIncidentContent($incidents)
    {
        // Extract common keywords and themes from incident content
        $allContent = $incidents->pluck('content')->filter()->implode(' ');
        
        // Common safety-related keywords to look for
        $safetyKeywords = [
            'máy móc', 'thiết bị', 'máy', 'dây chuyền', 'sản xuất',
            'hóa chất', 'hóa chất độc hại', 'khí gas', 'nhiệt độ cao',
            'cao', 'thang', 'cầu thang', 'giàn giáo', 'leo trèo',
            'điện', 'dây điện', 'ngắn mạch', 'chập điện',
            'xe', 'xe nâng', 'xe tải', 'giao thông', 'vận chuyển',
            'cắt', 'hàn', 'mài', 'khoan', 'đục', 'búa',
            'bảo hộ', 'mũ bảo hiểm', 'găng tay', 'kính mắt',
            'sàn', 'trơn', 'ướt', 'dầu', 'nước', 'bùn',
            'tiếng ồn', 'rung động', 'bụi', 'khói', 'hơi độc'
        ];

        $keywordCounts = [];
        foreach ($safetyKeywords as $keyword) {
            $count = substr_count(strtolower($allContent), strtolower($keyword));
            if ($count > 0) {
                $keywordCounts[$keyword] = $count;
            }
        }

        // Sort by frequency
        arsort($keywordCounts);

        return [
            'total_content_length' => strlen($allContent),
            'average_content_length' => $incidents->where('content', '!=', null)->avg(function($incident) {
                return strlen($incident->content ?? '');
            }),
            'common_keywords' => array_slice($keywordCounts, 0, 10, true),
            'content_themes' => $this->identifyContentThemes($incidents),
        ];
    }

    private function identifyContentThemes($incidents)
    {
        $themes = [
            'machinery' => ['máy móc', 'thiết bị', 'máy', 'dây chuyền'],
            'chemical' => ['hóa chất', 'hóa chất độc hại', 'khí gas', 'nhiệt độ cao'],
            'height' => ['cao', 'thang', 'cầu thang', 'giàn giáo', 'leo trèo'],
            'electrical' => ['điện', 'dây điện', 'ngắn mạch', 'chập điện'],
            'transport' => ['xe', 'xe nâng', 'xe tải', 'giao thông', 'vận chuyển'],
            'tools' => ['cắt', 'hàn', 'mài', 'khoan', 'đục', 'búa'],
            'ppe' => ['bảo hộ', 'mũ bảo hiểm', 'găng tay', 'kính mắt'],
            'environment' => ['sàn', 'trơn', 'ướt', 'dầu', 'nước', 'bùn'],
            'hazardous' => ['tiếng ồn', 'rung động', 'bụi', 'khói', 'hơi độc']
        ];

        $themeCounts = [];
        foreach ($themes as $theme => $keywords) {
            $count = 0;
            foreach ($incidents as $incident) {
                if ($incident->content) {
                    foreach ($keywords as $keyword) {
                        if (stripos($incident->content, $keyword) !== false) {
                            $count++;
                            break; // Count incident only once per theme
                        }
                    }
                }
            }
            if ($count > 0) {
                $themeCounts[$theme] = $count;
            }
        }

        return $themeCounts;
    }

    private function buildIncidentAnalysisPrompt($incidentData, $companyData, $statisticalData, $locale)
    {
        $language = $locale === 'ja' ? 'Japanese' : 'Vietnamese';
        
        return "You are a safety management AI assistant with expertise in workplace safety analysis. Analyze the following comprehensive data and provide detailed insights in {$language}.

COMPANY PROFILE:
- Name: {$companyData['name']}
- Industry: {$companyData['industry']}
- Company Size: {$companyData['size']} ({$companyData['employee_count']} employees)
- Business Description: {$companyData['description']}
- Address: {$companyData['address']}

STATISTICAL OVERVIEW:
- Total Incidents: {$statisticalData['total_incidents']}
- Incidents by Severity: " . json_encode($statisticalData['incidents_by_severity']) . "
- Incidents by Status: " . json_encode($statisticalData['incidents_by_status']) . "
- Incidents by Location: " . json_encode($statisticalData['incidents_by_location']) . "
- Monthly Trend: " . json_encode($statisticalData['incidents_by_month']) . "

CONTENT ANALYSIS:
- Common Safety Keywords: " . json_encode($statisticalData['content_analysis']['common_keywords']) . "
- Content Themes: " . json_encode($statisticalData['content_analysis']['content_themes']) . "
- Average Content Length: " . round($statisticalData['content_analysis']['average_content_length'], 2) . " characters

DETAILED INCIDENT DATA (with full content):
" . json_encode($incidentData, JSON_PRETTY_PRINT) . "

RECENT INCIDENTS SAMPLE (with content):
" . json_encode($statisticalData['recent_incidents'], JSON_PRETTY_PRINT) . "

Based on this comprehensive data, especially focusing on the detailed incident content and company description, provide detailed analysis in the following JSON format:
{
    \"department_analysis\": {
        \"high_risk_departments\": [\"specific department names based on incident patterns and content analysis\"],
        \"analysis\": \"Detailed analysis of which departments have most incidents, including specific reasons and patterns from incident content\",
        \"risk_factors\": [\"specific risk factors identified from incident content analysis\"],
        \"department_recommendations\": [\"specific recommendations for each high-risk department based on content themes\"]
    },
    \"location_analysis\": {
        \"high_risk_locations\": [\"specific location names with highest incident rates\"],
        \"analysis\": \"Detailed analysis of incident-prone locations with specific safety concerns from incident content\",
        \"location_patterns\": [\"specific patterns observed in each location from incident descriptions\"],
        \"location_recommendations\": [\"specific safety measures for each high-risk location based on content analysis\"]
    },
    \"content_analysis\": {
        \"main_themes\": [\"primary safety themes identified from incident content\"],
        \"common_issues\": [\"most frequently mentioned safety issues in incident content\"],
        \"equipment_concerns\": [\"equipment-related issues mentioned in incident content\"],
        \"environmental_factors\": [\"environmental safety factors identified from content\"],
        \"human_factors\": [\"human error patterns identified from incident content\"]
    },
    \"trend_analysis\": {
        \"patterns\": [\"specific patterns identified from statistical data and content analysis\"],
        \"analysis\": \"Comprehensive analysis of incident trends, seasonal patterns, and content-based insights\",
        \"severity_trends\": \"Analysis of severity level changes over time based on content themes\",
        \"frequency_analysis\": \"Analysis of incident frequency and timing patterns from content\"
    },
    \"company_specific_analysis\": {
        \"industry_risks\": \"Analysis of industry-specific risks based on company description\",
        \"company_size_impact\": \"How company size affects safety patterns based on description\",
        \"facility_analysis\": \"Analysis of facility-specific risks from company description\",
        \"operational_risks\": \"Operational risks identified from company description and incident content\"
    },
    \"statistical_insights\": {
        \"key_findings\": [\"specific statistical findings from the data and content analysis\"],
        \"correlations\": [\"correlations between different factors from content and statistics\"],
        \"anomalies\": [\"unusual patterns or outliers identified from content analysis\"]
    },
    \"recommendations\": [\"specific, actionable recommendations based on comprehensive analysis of content and company description\"],
    \"priority_actions\": [\"immediate priority actions based on content analysis and company profile\"]
}

IMPORTANT: 
1. Focus heavily on the detailed incident content and company description
2. Extract specific safety issues, equipment problems, environmental factors, and human factors from the incident content
3. Use the company description to understand the business context and tailor recommendations accordingly
4. Provide specific examples from the incident content in your analysis
5. CRITICAL: Respond ONLY with valid JSON format. Do not include any loading messages, explanations, or additional text
6. Start your response immediately with { and end with }
7. Do not include any loading phrases in your response

Your response must be valid JSON only.";
    }

    private function buildCompanyAnalysisPrompt($companyData, $incidentData, $locale)
    {
        $language = $locale === 'ja' ? 'Japanese' : 'Vietnamese';
        
        return "You are a business safety consultant AI. Analyze the company profile and provide safety recommendations in {$language}.

Company Information:
" . json_encode($companyData, JSON_PRETTY_PRINT) . "

Recent Incidents:
" . json_encode($incidentData, JSON_PRETTY_PRINT) . "

Please provide analysis in the following JSON format:
{
    \"company_profile\": {
        \"business_type\": \"Analysis of business type and operations\",
        \"scale_assessment\": \"Assessment of company scale and facilities\",
        \"risk_factors\": [\"risk1\", \"risk2\"]
    },
    \"safety_recommendations\": [
        {
            \"category\": \"Equipment Safety\",
            \"recommendation\": \"Specific recommendation\",
            \"priority\": \"High/Medium/Low\"
        }
    ],
    \"facility_analysis\": {
        \"assessment\": \"Analysis of facility scale and safety measures\",
        \"improvements\": [\"improvement1\", \"improvement2\"]
    }
}

CRITICAL: Respond ONLY with valid JSON format. Do not include any loading messages, explanations, or additional text. Start with { and end with }.";
    }

    private function buildSafetyRecommendationsPrompt($companyData, $incidentData, $locale)
    {
        $language = $locale === 'ja' ? 'Japanese' : 'Vietnamese';
        
        return "You are a predictive safety AI. Analyze the company and incident data to provide future safety warnings and recommendations in {$language}.

Company Information:
" . json_encode($companyData, JSON_PRETTY_PRINT) . "

Incident History:
" . json_encode($incidentData, JSON_PRETTY_PRINT) . "

Please provide analysis in the following JSON format:
{
    \"future_warnings\": [
        {
            \"type\": \"Equipment Failure\",
            \"warning\": \"Specific warning about potential issues\",
            \"timeframe\": \"Next 30 days\",
            \"severity\": \"High/Medium/Low\"
        }
    ],
    \"preventive_measures\": [
        {
            \"measure\": \"Specific preventive action\",
            \"department\": \"Affected department\",
            \"priority\": \"High/Medium/Low\"
        }
    ],
    \"equipment_monitoring\": {
        \"critical_equipment\": [\"equipment1\", \"equipment2\"],
        \"monitoring_recommendations\": [\"recommendation1\", \"recommendation2\"]
    }
}

CRITICAL: Respond ONLY with valid JSON format. Do not include any loading messages, explanations, or additional text. Start with { and end with }.";
    }

    private function buildIncidentResolutionPrompt($incidentData, $companyData, $locale)
    {
        $language = $locale === 'ja' ? 'Japanese' : 'Vietnamese';
        
        return "You are an incident response AI. Provide specific resolution steps for this incident in {$language}.

Incident Details:
" . json_encode($incidentData, JSON_PRETTY_PRINT) . "

Company Context:
" . json_encode($companyData, JSON_PRETTY_PRINT) . "

Please provide analysis in the following JSON format:
{
    \"immediate_actions\": [
        {
            \"action\": \"Specific immediate action\",
            \"responsible\": \"Who should do it\",
            \"timeline\": \"When to complete\"
        }
    ],
    \"investigation_steps\": [
        {
            \"step\": \"Investigation step\",
            \"purpose\": \"Why this step is needed\",
            \"tools_needed\": [\"tool1\", \"tool2\"]
        }
    ],
    \"prevention_measures\": [
        {
            \"measure\": \"Preventive action\",
            \"implementation\": \"How to implement\",
            \"timeline\": \"When to implement\"
        }
    ],
    \"follow_up_actions\": [
        {
            \"action\": \"Follow-up action\",
            \"timeline\": \"When to complete\",
            \"responsible\": \"Who is responsible\"
        }
    ]
}

CRITICAL: Respond ONLY with valid JSON format. Do not include any loading messages, explanations, or additional text. Start with { and end with }.";
    }

    private function parseIncidentAnalysisResponse($response, $locale)
    {
        try {
            // Log the raw response for debugging
            Log::info('Gemini AI Raw Response:', $response);
            
            $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Clean the content - remove any loading messages or non-JSON text
            $content = $this->cleanJsonResponse($content);
            
            Log::info('Cleaned Content:', ['content' => $content]);
            
            $decoded = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error: ' . json_last_error_msg());
                Log::error('Content that failed to decode: ' . $content);
                return $this->getDefaultIncidentAnalysis($locale);
            }
            
            return $decoded ?: $this->getDefaultIncidentAnalysis($locale);
        } catch (\Exception $e) {
            Log::error('Parse Incident Analysis Response Error: ' . $e->getMessage());
            return $this->getDefaultIncidentAnalysis($locale);
        }
    }

    private function parseCompanyAnalysisResponse($response, $locale)
    {
        try {
            $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $content = $this->cleanJsonResponse($content);
            
            $decoded = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Company Analysis JSON Decode Error: ' . json_last_error_msg());
                return $this->getDefaultCompanyAnalysis($locale);
            }
            
            return $decoded ?: $this->getDefaultCompanyAnalysis($locale);
        } catch (\Exception $e) {
            Log::error('Parse Company Analysis Response Error: ' . $e->getMessage());
            return $this->getDefaultCompanyAnalysis($locale);
        }
    }

    private function parseSafetyRecommendationsResponse($response, $locale)
    {
        try {
            $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $content = $this->cleanJsonResponse($content);
            
            $decoded = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Safety Recommendations JSON Decode Error: ' . json_last_error_msg());
                return $this->getDefaultSafetyRecommendations($locale);
            }
            
            return $decoded ?: $this->getDefaultSafetyRecommendations($locale);
        } catch (\Exception $e) {
            Log::error('Parse Safety Recommendations Response Error: ' . $e->getMessage());
            return $this->getDefaultSafetyRecommendations($locale);
        }
    }

    private function parseIncidentResolutionResponse($response, $locale)
    {
        try {
            $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $content = $this->cleanJsonResponse($content);
            
            $decoded = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Incident Resolution JSON Decode Error: ' . json_last_error_msg());
                return $this->getDefaultIncidentResolution($locale);
            }
            
            return $decoded ?: $this->getDefaultIncidentResolution($locale);
        } catch (\Exception $e) {
            Log::error('Parse Incident Resolution Response Error: ' . $e->getMessage());
            return $this->getDefaultIncidentResolution($locale);
        }
    }

    private function getDefaultIncidentAnalysis($locale)
    {
        if ($locale === 'ja') {
            return [
                'department_analysis' => [
                    'high_risk_departments' => ['製造部', 'メンテナンス部'],
                    'analysis' => 'インシデントデータの分析中です。詳細な分析結果をお待ちください。'
                ],
                'location_analysis' => [
                    'high_risk_locations' => ['工場フロア', '機械室'],
                    'analysis' => '場所別インシデント分析を実行中です。'
                ],
                'trend_analysis' => [
                    'patterns' => ['設備故障', '人的要因'],
                    'analysis' => 'トレンド分析を実行中です。'
                ],
                'recommendations' => ['安全研修の実施', '設備点検の強化']
            ];
        }

        return [
            'department_analysis' => [
                'high_risk_departments' => ['Sản xuất', 'Bảo trì'],
                'analysis' => 'Đang phân tích dữ liệu sự cố. Vui lòng chờ kết quả phân tích chi tiết.'
            ],
            'location_analysis' => [
                'high_risk_locations' => ['Tầng sản xuất', 'Phòng máy'],
                'analysis' => 'Đang thực hiện phân tích sự cố theo vị trí.'
            ],
            'trend_analysis' => [
                'patterns' => ['Hỏng hóc thiết bị', 'Yếu tố con người'],
                'analysis' => 'Đang thực hiện phân tích xu hướng.'
            ],
            'recommendations' => ['Tổ chức đào tạo an toàn', 'Tăng cường kiểm tra thiết bị']
        ];
    }

    private function getEnhancedDefaultIncidentAnalysis($incidents, $company, $locale)
    {
        // Analyze actual incident data to provide meaningful insights
        $departments = $incidents->pluck('location')->filter()->unique()->values()->toArray();
        $severities = $incidents->pluck('severity')->countBy()->toArray();
        $statuses = $incidents->pluck('status')->countBy()->toArray();
        
        // Add analysis note
        $analysisNote = $locale === 'ja' ? 
            "※ この分析は実際のインシデントデータに基づいています。" :
            "※ Phân tích này dựa trên dữ liệu sự cố thực tế.";
        
        // Get most common locations
        $locationCounts = $incidents->pluck('location')->countBy()->sortDesc();
        $highRiskLocations = $locationCounts->take(3)->keys()->toArray();
        
        // Get most common severities
        $severityCounts = $incidents->pluck('severity')->countBy()->sortDesc();
        $mostCommonSeverity = $severityCounts->keys()->first();
        
        if ($locale === 'ja') {
            return [
                'department_analysis' => [
                    'high_risk_departments' => $departments ?: ['製造部', 'メンテナンス部'],
                    'analysis' => "実際のインシデントデータに基づく分析。総インシデント数: {$incidents->count()}件。最も多い重大度: {$mostCommonSeverity}。"
                ],
                'location_analysis' => [
                    'high_risk_locations' => $highRiskLocations ?: ['工場フロア', '機械室'],
                    'analysis' => "場所別インシデント分析。高リスク場所: " . implode(', ', $highRiskLocations) . "。"
                ],
                'content_analysis' => [
                    'main_themes' => ['安全プロトコル', '設備管理', '従業員訓練'],
                    'common_issues' => ['安全手順の不遵守', '設備の不具合', '人的エラー'],
                    'equipment_concerns' => ['機械の故障', '安全装置の不備'],
                    'environmental_factors' => ['作業環境', '照明不足'],
                    'human_factors' => ['疲労', '集中力不足', '経験不足']
                ],
                'trend_analysis' => [
                    'patterns' => ['月間インシデント傾向', '重大度分布'],
                    'analysis' => "インシデント統計: 重大度別 " . json_encode($severityCounts) . "、ステータス別 " . json_encode($statuses) . "。",
                    'severity_trends' => "最も多い重大度: {$mostCommonSeverity}",
                    'frequency_analysis' => "総インシデント数: {$incidents->count()}件"
                ],
                'recommendations' => [
                    '安全プロトコルの見直しと強化',
                    '高リスク場所での追加安全対策',
                    '従業員の安全訓練プログラムの実施',
                    '設備の定期点検とメンテナンス',
                    'インシデント報告システムの改善'
                ],
                'priority_actions' => [
                    '即座に実施: 高リスク場所の安全点検',
                    '短期: 安全プロトコルの更新',
                    '中期: 従業員訓練プログラムの実施'
                ],
                'analysis_note' => $analysisNote
            ];
        } else {
            return [
                'department_analysis' => [
                    'high_risk_departments' => $departments ?: ['Sản xuất', 'Bảo trì'],
                    'analysis' => "Phân tích dựa trên dữ liệu sự cố thực tế. Tổng số sự cố: {$incidents->count()} vụ. Mức độ nghiêm trọng phổ biến nhất: {$mostCommonSeverity}."
                ],
                'location_analysis' => [
                    'high_risk_locations' => $highRiskLocations ?: ['Tầng sản xuất', 'Phòng máy'],
                    'analysis' => "Phân tích sự cố theo vị trí. Các vị trí rủi ro cao: " . implode(', ', $highRiskLocations) . "."
                ],
                'content_analysis' => [
                    'main_themes' => ['Quy trình an toàn', 'Quản lý thiết bị', 'Đào tạo nhân viên'],
                    'common_issues' => ['Không tuân thủ quy trình an toàn', 'Hỏng hóc thiết bị', 'Lỗi con người'],
                    'equipment_concerns' => ['Hỏng hóc máy móc', 'Thiết bị an toàn không đầy đủ'],
                    'environmental_factors' => ['Môi trường làm việc', 'Ánh sáng không đủ'],
                    'human_factors' => ['Mệt mỏi', 'Thiếu tập trung', 'Thiếu kinh nghiệm']
                ],
                'trend_analysis' => [
                    'patterns' => ['Xu hướng sự cố theo tháng', 'Phân bố mức độ nghiêm trọng'],
                    'analysis' => "Thống kê sự cố: Theo mức độ " . json_encode($severityCounts) . ", Theo trạng thái " . json_encode($statuses) . ".",
                    'severity_trends' => "Mức độ nghiêm trọng phổ biến nhất: {$mostCommonSeverity}",
                    'frequency_analysis' => "Tổng số sự cố: {$incidents->count()} vụ"
                ],
                'recommendations' => [
                    'Rà soát và tăng cường quy trình an toàn',
                    'Thực hiện biện pháp an toàn bổ sung tại các vị trí rủi ro cao',
                    'Triển khai chương trình đào tạo an toàn cho nhân viên',
                    'Kiểm tra và bảo trì thiết bị định kỳ',
                    'Cải thiện hệ thống báo cáo sự cố'
                ],
                'priority_actions' => [
                    'Thực hiện ngay: Kiểm tra an toàn tại các vị trí rủi ro cao',
                    'Ngắn hạn: Cập nhật quy trình an toàn',
                    'Trung hạn: Triển khai chương trình đào tạo nhân viên'
                ],
                'analysis_note' => $analysisNote
            ];
        }
    }

    private function getEnhancedDefaultCompanyAnalysis($company, $incidents, $locale)
    {
        // Analyze actual company and incident data
        $incidentCount = $incidents->count();
        $severityCounts = $incidents->pluck('severity')->countBy()->toArray();
        $locationCounts = $incidents->pluck('location')->countBy()->toArray();
        
        // Get company info
        $companyName = $company->name ?? 'Công ty';
        $companyIndustry = $company->industry ?? 'Sản xuất';
        $companySize = $company->size ?? 'Vừa';
        $employeeCount = $company->employee_count ?? 100;
        $companyDescription = $company->description ?? 'Mô tả công ty không có sẵn';
        
        // Analyze business type based on industry and incidents
        $businessType = $this->analyzeBusinessType($companyIndustry, $incidents, $locale);
        $scaleAssessment = $this->assessCompanyScale($employeeCount, $incidentCount, $locale);
        $riskFactors = $this->identifyRiskFactors($incidents, $companyDescription, $locale);
        
        if ($locale === 'ja') {
            return [
                'company_profile' => [
                    'business_type' => $businessType,
                    'scale_assessment' => $scaleAssessment,
                    'risk_factors' => $riskFactors
                ],
                'safety_recommendations' => [
                    [
                        'category' => '設備安全',
                        'recommendation' => "インシデント数 {$incidentCount} 件に基づく設備点検の強化",
                        'priority' => 'High'
                    ],
                    [
                        'category' => '従業員訓練',
                        'recommendation' => '安全プロトコルの定期的な見直しと訓練の実施',
                        'priority' => 'Medium'
                    ],
                    [
                        'category' => '環境管理',
                        'recommendation' => '作業環境の改善と安全設備の整備',
                        'priority' => 'Medium'
                    ]
                ],
                'facility_analysis' => [
                    'assessment' => "{$companyName}の施設分析: 従業員数 {$employeeCount}名、インシデント数 {$incidentCount}件",
                    'improvements' => [
                        '高リスク場所の特定と対策',
                        '安全設備の点検と更新',
                        '作業環境の改善'
                    ]
                ]
            ];
        } else {
            return [
                'company_profile' => [
                    'business_type' => $businessType,
                    'scale_assessment' => $scaleAssessment,
                    'risk_factors' => $riskFactors
                ],
                'safety_recommendations' => [
                    [
                        'category' => 'An toàn thiết bị',
                        'recommendation' => "Tăng cường kiểm tra thiết bị dựa trên {$incidentCount} sự cố đã xảy ra",
                        'priority' => 'High'
                    ],
                    [
                        'category' => 'Đào tạo nhân viên',
                        'recommendation' => 'Rà soát và đào tạo quy trình an toàn định kỳ',
                        'priority' => 'Medium'
                    ],
                    [
                        'category' => 'Quản lý môi trường',
                        'recommendation' => 'Cải thiện môi trường làm việc và trang bị thiết bị an toàn',
                        'priority' => 'Medium'
                    ]
                ],
                'facility_analysis' => [
                    'assessment' => "Phân tích cơ sở {$companyName}: {$employeeCount} nhân viên, {$incidentCount} sự cố",
                    'improvements' => [
                        'Xác định và xử lý các vị trí rủi ro cao',
                        'Kiểm tra và cập nhật thiết bị an toàn',
                        'Cải thiện môi trường làm việc'
                    ]
                ]
            ];
        }
    }

    private function analyzeBusinessType($industry, $incidents, $locale)
    {
        $incidentCount = $incidents->count();
        $severityCounts = $incidents->pluck('severity')->countBy()->toArray();
        
        if ($locale === 'ja') {
            return "{$industry}業界の企業。インシデント数: {$incidentCount}件。主要な安全課題: " . implode(', ', array_keys($severityCounts)) . "。";
        } else {
            return "Công ty hoạt động trong lĩnh vực {$industry}. Số sự cố: {$incidentCount} vụ. Các vấn đề an toàn chính: " . implode(', ', array_keys($severityCounts)) . ".";
        }
    }

    private function assessCompanyScale($employeeCount, $incidentCount, $locale)
    {
        $scale = $employeeCount < 50 ? 'Small' : ($employeeCount < 200 ? 'Medium' : 'Large');
        $incidentRate = $employeeCount > 0 ? round(($incidentCount / $employeeCount) * 100, 2) : 0;
        
        if ($locale === 'ja') {
            return "企業規模: {$scale} ({$employeeCount}名)。インシデント率: {$incidentRate}%。";
        } else {
            return "Quy mô công ty: {$scale} ({$employeeCount} nhân viên). Tỷ lệ sự cố: {$incidentRate}%.";
        }
    }

    private function identifyRiskFactors($incidents, $companyDescription, $locale)
    {
        $riskFactors = [];
        
        // Analyze incident patterns
        $severityCounts = $incidents->pluck('severity')->countBy()->toArray();
        $locationCounts = $incidents->pluck('location')->countBy()->toArray();
        
        if (isset($severityCounts['High']) && $severityCounts['High'] > 0) {
            $riskFactors[] = $locale === 'ja' ? '高リスクインシデント' : 'Sự cố nghiêm trọng';
        }
        
        if (count($locationCounts) > 1) {
            $riskFactors[] = $locale === 'ja' ? '複数場所でのインシデント' : 'Sự cố tại nhiều vị trí';
        }
        
        // Analyze company description for risk keywords
        $description = strtolower($companyDescription);
        if (strpos($description, 'máy móc') !== false || strpos($description, 'thiết bị') !== false) {
            $riskFactors[] = $locale === 'ja' ? '設備関連リスク' : 'Rủi ro thiết bị';
        }
        
        if (strpos($description, 'hóa chất') !== false || strpos($description, 'chemical') !== false) {
            $riskFactors[] = $locale === 'ja' ? '化学物質リスク' : 'Rủi ro hóa chất';
        }
        
        return $riskFactors ?: [$locale === 'ja' ? '一般的な安全リスク' : 'Rủi ro an toàn chung'];
    }

    private function getEnhancedDefaultSafetyRecommendations($company, $incidents, $locale)
    {
        // Analyze actual incident data for safety recommendations
        $incidentCount = $incidents->count();
        $severityCounts = $incidents->pluck('severity')->countBy()->toArray();
        $locationCounts = $incidents->pluck('location')->countBy()->toArray();
        $statusCounts = $incidents->pluck('status')->countBy()->toArray();
        
        // Get company info
        $companyName = $company->name ?? 'Công ty';
        $employeeCount = $company->employee_count ?? 100;
        
        // Calculate incident rate
        $incidentRate = $employeeCount > 0 ? round(($incidentCount / $employeeCount) * 100, 2) : 0;
        
        // Identify high-risk areas
        $highRiskLocations = collect($locationCounts)->sortDesc()->take(3)->keys()->toArray();
        
        // Generate future warnings based on patterns
        $futureWarnings = $this->generateFutureWarnings($incidents, $locale);
        $preventiveMeasures = $this->generatePreventiveMeasures($incidents, $company, $locale);
        
        if ($locale === 'ja') {
            return [
                'future_warnings' => $futureWarnings,
                'preventive_measures' => $preventiveMeasures,
                'incident_summary' => [
                    'total_incidents' => $incidentCount,
                    'incident_rate' => $incidentRate,
                    'high_risk_locations' => $highRiskLocations,
                    'severity_distribution' => $severityCounts
                ]
            ];
        } else {
            return [
                'future_warnings' => $futureWarnings,
                'preventive_measures' => $preventiveMeasures,
                'incident_summary' => [
                    'total_incidents' => $incidentCount,
                    'incident_rate' => $incidentRate,
                    'high_risk_locations' => $highRiskLocations,
                    'severity_distribution' => $severityCounts
                ]
            ];
        }
    }

    private function generateFutureWarnings($incidents, $locale)
    {
        $incidentCount = $incidents->count();
        $severityCounts = $incidents->pluck('severity')->countBy()->toArray();
        $locationCounts = $incidents->pluck('location')->countBy()->toArray();
        
        $warnings = [];
        
        // High incident rate warning
        if ($incidentCount > 10) {
            $warnings[] = [
                'type' => $locale === 'ja' ? '高インシデント率' : 'Tỷ lệ sự cố cao',
                'warning' => $locale === 'ja' ? 
                    "インシデント数が {$incidentCount} 件と高く、継続的な監視が必要です。" :
                    "Số sự cố {$incidentCount} vụ khá cao, cần theo dõi liên tục.",
                'severity' => 'High',
                'timeframe' => $locale === 'ja' ? '即座' : 'Ngay lập tức'
            ];
        }
        
        // High severity incidents warning
        if (isset($severityCounts['High']) && $severityCounts['High'] > 0) {
            $warnings[] = [
                'type' => $locale === 'ja' ? '高重大度インシデント' : 'Sự cố nghiêm trọng',
                'warning' => $locale === 'ja' ? 
                    "高重大度インシデントが {$severityCounts['High']} 件発生しています。" :
                    "Đã xảy ra {$severityCounts['High']} sự cố nghiêm trọng.",
                'severity' => 'High',
                'timeframe' => $locale === 'ja' ? '即座' : 'Ngay lập tức'
            ];
        }
        
        // Multiple location warning
        if (count($locationCounts) > 2) {
            $warnings[] = [
                'type' => $locale === 'ja' ? '複数場所でのインシデント' : 'Sự cố tại nhiều vị trí',
                'warning' => $locale === 'ja' ? 
                    "複数の場所でインシデントが発生しており、全体的な安全対策が必要です。" :
                    "Sự cố xảy ra tại nhiều vị trí, cần biện pháp an toàn tổng thể.",
                'severity' => 'Medium',
                'timeframe' => $locale === 'ja' ? '1週間以内' : 'Trong vòng 1 tuần'
            ];
        }
        
        return $warnings ?: [
            [
                'type' => $locale === 'ja' ? '一般的な安全監視' : 'Giám sát an toàn chung',
                'warning' => $locale === 'ja' ? 
                    "継続的な安全監視と予防策の実施を推奨します。" :
                    "Khuyến nghị giám sát an toàn liên tục và thực hiện biện pháp phòng ngừa.",
                'severity' => 'Low',
                'timeframe' => $locale === 'ja' ? '1ヶ月以内' : 'Trong vòng 1 tháng'
            ]
        ];
    }

    private function generatePreventiveMeasures($incidents, $company, $locale)
    {
        $incidentCount = $incidents->count();
        $severityCounts = $incidents->pluck('severity')->countBy()->toArray();
        $locationCounts = $incidents->pluck('location')->countBy()->toArray();
        
        $measures = [];
        
        // Equipment safety measures
        if (isset($severityCounts['High']) && $severityCounts['High'] > 0) {
            $measures[] = [
                'measure' => $locale === 'ja' ? 
                    "高重大度インシデント対策として設備の緊急点検を実施" :
                    "Thực hiện kiểm tra khẩn cấp thiết bị do có sự cố nghiêm trọng",
                'department' => $locale === 'ja' ? 'メンテナンス部' : 'Bộ phận bảo trì',
                'priority' => 'High'
            ];
        }
        
        // Training measures
        if ($incidentCount > 5) {
            $measures[] = [
                'measure' => $locale === 'ja' ? 
                    "インシデント数増加に対応した安全訓練の実施" :
                    "Tổ chức đào tạo an toàn do số sự cố tăng",
                'department' => $locale === 'ja' ? '人事部' : 'Bộ phận nhân sự',
                'priority' => 'High'
            ];
        }
        
        // Location-specific measures
        $highRiskLocations = collect($locationCounts)->sortDesc()->take(2)->keys()->toArray();
        foreach ($highRiskLocations as $location) {
            $measures[] = [
                'measure' => $locale === 'ja' ? 
                    "{$location}での追加安全対策の実施" :
                    "Thực hiện biện pháp an toàn bổ sung tại {$location}",
                'department' => $locale === 'ja' ? '安全部' : 'Bộ phận an toàn',
                'priority' => 'Medium'
            ];
        }
        
        // General preventive measures
        $measures[] = [
            'measure' => $locale === 'ja' ? 
                "定期的な安全点検とリスクアセスメントの実施" :
                "Thực hiện kiểm tra an toàn định kỳ và đánh giá rủi ro",
            'department' => $locale === 'ja' ? '全部門' : 'Tất cả bộ phận',
            'priority' => 'Medium'
        ];
        
        return $measures;
    }

    private function getDefaultCompanyAnalysis($locale)
    {
        if ($locale === 'ja') {
            return [
                'company_profile' => [
                    'business_type' => '会社の事業内容を分析中です。',
                    'scale_assessment' => '会社規模の評価を実行中です。',
                    'risk_factors' => ['設備リスク', '人的リスク']
                ],
                'safety_recommendations' => [
                    [
                        'category' => '設備安全',
                        'recommendation' => '定期的な設備点検の実施',
                        'priority' => 'High'
                    ]
                ],
                'facility_analysis' => [
                    'assessment' => '施設の安全性評価を実行中です。',
                    'improvements' => ['安全設備の追加', '点検頻度の向上']
                ]
            ];
        }

        return [
            'company_profile' => [
                'business_type' => 'Đang phân tích loại hình kinh doanh của công ty.',
                'scale_assessment' => 'Đang đánh giá quy mô công ty.',
                'risk_factors' => ['Rủi ro thiết bị', 'Rủi ro con người']
            ],
            'safety_recommendations' => [
                [
                    'category' => 'An toàn thiết bị',
                    'recommendation' => 'Thực hiện kiểm tra thiết bị định kỳ',
                    'priority' => 'High'
                ]
            ],
            'facility_analysis' => [
                'assessment' => 'Đang đánh giá an toàn cơ sở vật chất.',
                'improvements' => ['Bổ sung thiết bị an toàn', 'Tăng tần suất kiểm tra']
            ]
        ];
    }

    private function getDefaultSafetyRecommendations($locale)
    {
        if ($locale === 'ja') {
            return [
                'future_warnings' => [
                    [
                        'type' => '設備故障',
                        'warning' => '古い設備の故障リスクが高まっています',
                        'timeframe' => '30日以内',
                        'severity' => 'Medium'
                    ]
                ],
                'preventive_measures' => [
                    [
                        'measure' => '設備の定期点検を実施',
                        'department' => 'メンテナンス部',
                        'priority' => 'High'
                    ]
                ],
                'equipment_monitoring' => [
                    'critical_equipment' => ['CNC機械', '溶接機'],
                    'monitoring_recommendations' => ['センサー設置', '定期点検']
                ]
            ];
        }

        return [
            'future_warnings' => [
                [
                    'type' => 'Hỏng hóc thiết bị',
                    'warning' => 'Rủi ro hỏng hóc thiết bị cũ đang tăng cao',
                    'timeframe' => 'Trong 30 ngày tới',
                    'severity' => 'Medium'
                ]
            ],
            'preventive_measures' => [
                [
                    'measure' => 'Thực hiện kiểm tra thiết bị định kỳ',
                    'department' => 'Bộ phận bảo trì',
                    'priority' => 'High'
                ]
            ],
            'equipment_monitoring' => [
                'critical_equipment' => ['Máy CNC', 'Máy hàn'],
                'monitoring_recommendations' => ['Lắp đặt cảm biến', 'Kiểm tra định kỳ']
            ]
        ];
    }

    private function getDefaultIncidentResolution($locale)
    {
        if ($locale === 'ja') {
            return [
                'immediate_actions' => [
                    [
                        'action' => '現場の安全確保',
                        'responsible' => '現場責任者',
                        'timeline' => '即座'
                    ]
                ],
                'investigation_steps' => [
                    [
                        'step' => '現場調査の実施',
                        'purpose' => '原因の特定',
                        'tools_needed' => ['カメラ', '測定器']
                    ]
                ],
                'prevention_measures' => [
                    [
                        'measure' => '安全手順の見直し',
                        'implementation' => '全従業員への周知',
                        'timeline' => '1週間以内'
                    ]
                ],
                'follow_up_actions' => [
                    [
                        'action' => '再発防止策の実施',
                        'timeline' => '1ヶ月以内',
                        'responsible' => '安全管理者'
                    ]
                ]
            ];
        }

        return [
            'immediate_actions' => [
                [
                    'action' => 'Đảm bảo an toàn hiện trường',
                    'responsible' => 'Người phụ trách hiện trường',
                    'timeline' => 'Ngay lập tức'
                ]
            ],
            'investigation_steps' => [
                [
                    'step' => 'Thực hiện điều tra hiện trường',
                    'purpose' => 'Xác định nguyên nhân',
                    'tools_needed' => ['Máy ảnh', 'Thiết bị đo']
                ]
            ],
            'prevention_measures' => [
                [
                    'measure' => 'Rà soát quy trình an toàn',
                    'implementation' => 'Thông báo đến toàn bộ nhân viên',
                    'timeline' => 'Trong vòng 1 tuần'
                ]
            ],
            'follow_up_actions' => [
                [
                    'action' => 'Thực hiện biện pháp ngăn ngừa tái diễn',
                    'timeline' => 'Trong vòng 1 tháng',
                    'responsible' => 'Người quản lý an toàn'
                ]
            ]
        ];
    }
}
