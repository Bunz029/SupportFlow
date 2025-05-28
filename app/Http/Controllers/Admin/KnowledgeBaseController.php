<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $articles = KnowledgeBase::with('category')->get();
        $categories = Category::all();
        return view('admin.knowledge-base.index', compact('articles', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $knowledge_base = new KnowledgeBase($request->all());
        $knowledge_base->slug = Str::slug($request->title);
        $knowledge_base->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Article created successfully.']);
        }
        return redirect()->route('admin.knowledge-base.index')
            ->with('success', 'Article created successfully.');
    }

    public function edit(KnowledgeBase $knowledge_base)
    {
        $categories = Category::all();
        return response()->json([
            'article' => [
                'id' => $knowledge_base->id,
                'title' => $knowledge_base->title,
                'category_id' => $knowledge_base->category_id,
                'excerpt' => $knowledge_base->excerpt,
                'content' => $knowledge_base->content,
            ],
            'categories' => $categories
        ]);
    }

    public function update(Request $request, KnowledgeBase $knowledge_base)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $knowledge_base->fill($request->all());
        $knowledge_base->slug = Str::slug($request->title);
        $knowledge_base->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Article updated successfully.']);
        }
        return redirect()->route('admin.knowledge-base.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(KnowledgeBase $knowledge_base)
    {
        $knowledge_base->delete();

        return response()->json([
            'message' => 'Article deleted successfully.'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $articles = KnowledgeBase::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->with('category')
            ->get();

        return response()->json($articles);
    }

    public function incrementViews(KnowledgeBase $article)
    {
        $article->increment('views_count');
        return response()->json(['views_count' => $article->views_count]);
    }
} 