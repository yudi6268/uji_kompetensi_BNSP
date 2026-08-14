<?php
$metrics = \App\Models\HealthMetric::all();
$grouped = [];
foreach ($metrics as $m) {
    $key = $m->user_id . '_' . $m->age_group . '_' . $m->gender;
    if (!isset($grouped[$key])) {
        $grouped[$key] = $m;
    } else {
        $grouped[$key]->patient_count += $m->patient_count;
        $grouped[$key]->save();
        $m->delete();
    }
}
echo "Fixed DB records.\n";
