<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = Auth::user()->healthMetrics()->orderBy('created_at', 'asc')->get();
        $articles = \App\Models\Article::latest()->get();
        return view('dashboard', compact('metrics', 'articles'));
    }

    public function storeMetric(Request $request)
    {
        $request->validate([
            'age_group' => 'required|array|min:1',
            'age_group.*' => 'required|in:30-40,41-50,51-60,60+',
            'gender' => 'required|array|min:1',
            'gender.*' => 'required|in:Pria,Wanita',
            'patient_count' => 'required|array|min:1',
            'patient_count.*' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        foreach ($request->age_group as $index => $ageGroup) {
            $metric = $user->healthMetrics()->firstOrNew([
                'age_group' => $ageGroup,
                'gender' => $request->gender[$index],
            ]);
            $metric->patient_count = $request->patient_count[$index];
            $metric->save();
        }

        return redirect()->route('dashboard')->with('success', 'Data jumlah penderita berhasil dicatat secara massal.');
    }

    public function updateMetric(Request $request, \App\Models\HealthMetric $metric)
    {
        if ($metric->user_id !== Auth::id()) abort(403);

        $request->validate([
            'age_group' => 'required|in:30-40,41-50,51-60,60+',
            'gender' => 'required|in:Pria,Wanita',
            'patient_count' => 'required|integer|min:1',
        ]);

        // Check if changing to an already existing category
        if ($metric->age_group !== $request->age_group || $metric->gender !== $request->gender) {
            $existing = Auth::user()->healthMetrics()->where('age_group', $request->age_group)
                                                     ->where('gender', $request->gender)
                                                     ->first();
            if ($existing) {
                // Merge into existing and delete the current one
                $existing->patient_count += $request->patient_count;
                $existing->save();
                $metric->delete();
                return redirect()->route('dashboard')->with('success', 'Data grafik berhasil diperbarui dan digabung dengan kategori yang sama.');
            }
        }

        $metric->update([
            'age_group' => $request->age_group,
            'gender' => $request->gender,
            'patient_count' => $request->patient_count,
        ]);

        return redirect()->route('dashboard')->with('success', 'Data grafik berhasil diperbarui.');
    }

    public function destroyMetric(\App\Models\HealthMetric $metric)
    {
        if ($metric->user_id !== Auth::id()) abort(403);
        $metric->delete();
        return redirect()->route('dashboard')->with('success', 'Data grafik berhasil dihapus.');
    }
}
