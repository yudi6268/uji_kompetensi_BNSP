<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->get();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'videos.*' => 'nullable|mimes:mp4,webm|max:20480',
            'image_urls.*' => 'nullable|url',
            'video_urls.*' => 'nullable|url',
        ]);

        $data = $request->only(['title', 'content']);

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageUrls[] = '/storage/' . $file->store('articles/images', 'public');
            }
        }
        if ($request->filled('image_urls')) {
            foreach ($request->image_urls as $url) {
                if (!empty($url)) $imageUrls[] = $url;
            }
        }

        $videoUrls = [];
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $file) {
                $videoUrls[] = '/storage/' . $file->store('articles/videos', 'public');
            }
        }
        if ($request->filled('video_urls')) {
            foreach ($request->video_urls as $url) {
                if (!empty($url)) $videoUrls[] = $url;
            }
        }

        if (!empty($imageUrls)) $data['image_url'] = $imageUrls;
        if (!empty($videoUrls)) $data['video_url'] = $videoUrls;

        Article::create($data);

        return redirect()->route('articles.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'videos.*' => 'nullable|mimes:mp4,webm|max:20480',
            'image_urls.*' => 'nullable|url',
            'video_urls.*' => 'nullable|url',
        ]);

        $data = $request->only(['title', 'content']);

        // Check if any new images provided
        $hasNewImages = $request->hasFile('images') || (is_array($request->image_urls) && count(array_filter($request->image_urls)) > 0);
        
        if ($hasNewImages) {
            // Delete old images
            $oldImageUrls = $article->image_url ?? [];
            foreach ($oldImageUrls as $old) {
                if (Str::startsWith($old, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $old));
                }
            }
            
            $imageUrls = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imageUrls[] = '/storage/' . $file->store('articles/images', 'public');
                }
            }
            if ($request->filled('image_urls')) {
                foreach ($request->image_urls as $url) {
                    if (!empty($url)) $imageUrls[] = $url;
                }
            }
            $data['image_url'] = $imageUrls;
        }

        // Check if any new videos provided
        $hasNewVideos = $request->hasFile('videos') || (is_array($request->video_urls) && count(array_filter($request->video_urls)) > 0);
        
        if ($hasNewVideos) {
            // Delete old videos
            $oldVideoUrls = $article->video_url ?? [];
            foreach ($oldVideoUrls as $old) {
                if (Str::startsWith($old, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $old));
                }
            }
            
            $videoUrls = [];
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $file) {
                    $videoUrls[] = '/storage/' . $file->store('articles/videos', 'public');
                }
            }
            if ($request->filled('video_urls')) {
                foreach ($request->video_urls as $url) {
                    if (!empty($url)) $videoUrls[] = $url;
                }
            }
            $data['video_url'] = $videoUrls;
        }

        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        $images = $article->image_url ?? [];
        foreach ($images as $img) {
            if (Str::startsWith($img, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $img));
            }
        }

        $videos = $article->video_url ?? [];
        foreach ($videos as $vid) {
            if (Str::startsWith($vid, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $vid));
            }
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Berita berhasil dihapus.');
    }
}
