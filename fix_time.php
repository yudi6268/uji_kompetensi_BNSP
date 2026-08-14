<?php
\App\Models\Article::all()->each(function($a) {
    $a->created_at = $a->created_at->addHours(7);
    $a->updated_at = $a->updated_at->addHours(7);
    $a->save();
});
echo "Timestamps updated.\n";
