<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use Illuminate\Http\Request;

class LecturePageController extends Controller
{
    public function index()
    {
        // Sort by order (0 is treated as largest/last)
        $lecturePages = LecturePage::orderByRaw('CASE WHEN sort_order = 0 THEN 1 ELSE 0 END ASC')
            ->orderBy('sort_order', 'asc')
            ->get();
        return view('admin.lecture_pages.index', compact('lecturePages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Suggest next sort order number for convenience (optional usage in view)
        $nextSortOrder = LecturePage::max('sort_order') + 1;
        return view('admin.lecture_pages.create', compact('nextSortOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'thumbnail' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Auto-increment sort_order
        $sortOrder = LecturePage::max('sort_order') + 1;

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            // Save to public/storage/thumbnails
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $thumbnailPath = '/storage/' . $path;
        }

        LecturePage::create([
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => $sortOrder,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'thumbnail_path' => $thumbnailPath,
        ]);

        return redirect()->route('admin.lecture-pages.index')
            ->with('success', '講義ページを作成しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(LecturePage $lecturePage)
    {
        return view('admin.lecture_pages.show', compact('lecturePage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LecturePage $lecturePage)
    {
        return view('admin.lecture_pages.edit', compact('lecturePage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LecturePage $lecturePage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'description', 'sort_order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('thumbnail')) {
            // Save new file
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail_path'] = '/storage/' . $path;
        }

        if (!$data['is_active']) {
            $data['sort_order'] = 0;
        }

        $lecturePage->update($data);

        return redirect()->route('admin.lecture-pages.index')
            ->with('success', '講義ページを更新しました。');
    }

    public function destroy(LecturePage $lecturePage)
    {
        $lecturePage->delete();
        return redirect()->route('admin.lecture-pages.index')->with('success', '講義ページを削除しました。');
    }

    public function deactivate(LecturePage $lecturePage)
    {
        $lecturePage->update([
            'is_active' => false,
            'sort_order' => 0,
        ]);
        return redirect()->route('admin.lecture-pages.index')->with('success', '講義ページを停止（非公開）にし、表示順を0にしました。');
    }
}
