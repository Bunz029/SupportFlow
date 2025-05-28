<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\RoleHelper;

class KnowledgeBaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the articles.
     */
    public function index(Request $request)
    {
        $query = KnowledgeBase::with('category');

        // Remove visibility filter (no such column)
        // if (!$user || !RoleHelper::hasAnyRole($user, ['admin', 'agent'])) {
        //     $query->where('visibility', 'public');
        // }

        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Apply tag filter
        if ($request->has('tag')) {
            $query->withTag($request->tag);
        }

        // Apply search filter
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest()->paginate(10);
        $categories = Category::all();
        
        // Get popular tags
        $allTags = [];
        $articles->each(function($article) use (&$allTags) {
            if (!empty($article->tags)) {
                $allTags = array_merge($allTags, $article->tags);
            }
        });
        $tags = array_unique($allTags);
        
        return view('knowledgebase.index', compact('articles', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $this->authorize('create', KnowledgeBase::class);
        
        $categories = Category::all();
        $visibilityOptions = ['public', 'private', 'internal'];
        
        return view('knowledgebase.create', compact('categories', 'visibilityOptions'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', KnowledgeBase::class);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string|max:500',
        ]);
        
        KnowledgeBase::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'category_id' => $request->category_id,
            'excerpt' => $request->excerpt,
        ]);
        
        return redirect()->route('knowledgebase.index')
            ->with('success', 'Article created successfully!');
    }

    /**
     * Display the specified article.
     */
    public function show(KnowledgeBase $article)
    {
        $user = Auth::user();
        
        // Visibility check removed: all users can view articles
        
        // Increment view count
        $article->increment('views_count');
        
        $article->load('category');
        
        // Get related articles
        $relatedArticles = KnowledgeBase::where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->take(5)
            ->get();
        
        return view('knowledgebase.show', compact('article', 'relatedArticles'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(KnowledgeBase $article)
    {
        $this->authorize('update', $article);
        
        $categories = Category::all();
        $visibilityOptions = ['public', 'private', 'internal'];
        
        return view('knowledgebase.edit', compact('article', 'categories', 'visibilityOptions'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, KnowledgeBase $article)
    {
        $this->authorize('update', $article);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string|max:500',
        ]);
        
        $article->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'category_id' => $request->category_id,
            'excerpt' => $request->excerpt,
        ]);
        
        return redirect()->route('knowledgebase.show', $article)
            ->with('success', 'Article updated successfully!');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(KnowledgeBase $article)
    {
        $this->authorize('delete', $article);
        
        $article->delete();
        
        return redirect()->route('knowledgebase.index')
            ->with('success', 'Article deleted successfully!');
    }
} 