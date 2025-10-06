<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Traits\ActivityLogger;

class ActivityLogController extends Controller
{
    use ActivityLogger;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Menampilkan daftar activity log
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();
            
        // Filter berdasarkan user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('causer_id', $request->user_id);
        }
        
        // Filter berdasarkan model type
        if ($request->has('subject_type') && $request->subject_type) {
            $query->where('subject_type', $request->subject_type);
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter berdasarkan description
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        $perPage = $request->get('per_page', 20);
        $activities = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'result' => $activities
        ], 200);
    }
    
    /**
     * Menampilkan activity log untuk model tertentu
     * 
     * @param Request $request
     * @param string $modelType
     * @param int $modelId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showForModel(Request $request, string $modelType, int $modelId)
    {
        $activities = Activity::where('subject_type', $modelType)
            ->where('subject_id', $modelId)
            ->with(['causer'])
            ->latest()
            ->paginate($request->get('per_page', 10));
            
        return response()->json([
            'success' => true,
            'result' => $activities
        ], 200);
    }
    
    /**
     * Menampilkan activity log untuk user tertentu
     * 
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showForUser(Request $request, int $userId)
    {
        $activities = Activity::where('causer_id', $userId)
            ->with(['subject'])
            ->latest()
            ->paginate($request->get('per_page', 20));
            
        return response()->json([
            'success' => true,
            'result' => $activities
        ], 200);
    }
    
    /**
     * Menampilkan detail activity log
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $activity = Activity::with(['causer', 'subject'])->find($id);
        
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity log tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'result' => $activity
        ], 200);
    }
    
    /**
     * Menampilkan statistik activity log
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        $totalActivities = Activity::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();
            
        $activitiesByType = Activity::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('subject_type, COUNT(*) as count')
            ->groupBy('subject_type')
            ->get();
            
        $activitiesByUser = Activity::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->with('causer:id,name')
            ->selectRaw('causer_id, COUNT(*) as count')
            ->groupBy('causer_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
            
        $activitiesByDay = Activity::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json([
            'success' => true,
            'result' => [
                'total_activities' => $totalActivities,
                'activities_by_type' => $activitiesByType,
                'activities_by_user' => $activitiesByUser,
                'activities_by_day' => $activitiesByDay,
                'date_range' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ]
            ]
        ], 200);
    }
}