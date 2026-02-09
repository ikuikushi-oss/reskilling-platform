<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ZoomSyncController extends Controller
{
    public function sync(Request $request)
    {
        // Simple synchronous call for now. 
        // In production with many meetings, this should be queued.
        // User requested: "POST /admin/zoom/sync-mtgs"

        try {
            // Default look back 30 days
            $from = now()->subDays(30)->format('Y-m-d');
            $to = now()->format('Y-m-d');

            Artisan::call('zoom:sync-mtgs', [
                '--from' => $from,
                '--to' => $to,
            ]);

            $output = Artisan::output();

            return back()->with('success', 'Zoom実績同期を実行しました: ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Zoom同期に失敗しました: ' . $e->getMessage());
        }
    }
}
