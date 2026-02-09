<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoomSettingController extends Controller
{
    /**
     * Display the zoom settings page.
     */
    public function edit()
    {
        return view('admin.zoom.settings');
    }

    /**
     * Test the Zoom connection using configured credentials.
     */
    public function testConnection(\App\Services\Zoom\ZoomClient $zoomClient)
    {
        $token = $zoomClient->getAccessToken();

        if ($token) {
            return back()->with('success', 'Zoom接続に成功しました！ (Access Token取得完了)');
        }

        return back()->with('error', 'Zoom接続に失敗しました。認証情報を確認してください。');
    }
}
