@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'Knowledge Base')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Knowledge Base</h1>
        @if(Auth::user() && Auth::user()->isAdmin())
            <a href="{{ route('admin.knowledge-base.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Manage Articles
            </a>
        @endif
    </div>

    <!-- Search Form -->
    <div class="mb-8">
        <form method="GET" action="{{ route('knowledgebase.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="q" value="{{ request('q') }}" 
                       placeholder="Search articles..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="w-48">
                <select name="category_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search
            </button>
        </form>
    </div>

    @if($articles->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('knowledgebase.show', $article->slug) }}" class="hover:text-blue-600">
                                        {{ $article->title }}
                                    </a>
                                </h2>
                                <p class="text-sm text-gray-500 mb-4">{{ Str::limit($article->excerpt, 120) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4 text-sm">
                            <span class="text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                {{ $article->category->name ?? 'Uncategorized' }}
                            </span>
                            <div class="flex items-center gap-4">
                                <span class="text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ number_format($article->views_count) }}
                                </span>
                                <span class="text-gray-500">
                                    {{ $article->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request('q'))
                    No articles match your search criteria. Try different keywords or browse all articles.
                @else
                    No articles have been published yet.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection