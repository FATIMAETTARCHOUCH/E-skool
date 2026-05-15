<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

$lessons = Lesson::all();
$synced = 0;

foreach ($lessons as $lesson) {
    if ($lesson->group_id) {
        $lesson->groups()->sync([$lesson->group_id]);
        $synced++;
    }
}

echo "Synchronization complete. Synced $synced lessons. Total pivot entries: " . DB::table('lesson_group')->count();
