@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('knowledgebase.index') }}" class="text-blue-600 hover:underline">&larr; Back to Knowledge Base</a>
    </div>
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $article->title }}</h1>
        <div class="flex items-center gap-4 mb-4">
            <span class="text-sm text-blue-700 bg-blue-100 px-3 py-1 rounded-full">{{ $article->category->name ?? 'Uncategorized' }}</span>
            <span class="text-xs text-gray-400">Last updated {{ $article->updated_at->diffForHumans() }}</span>
            <span class="text-xs text-gray-400 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($article->views_count) }} views
            </span>
        </div>
        <div class="text-lg text-gray-700 mb-4">{{ $article->excerpt }}</div>
        <div class="prose max-w-none text-gray-800">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>
    @if($relatedArticles->count())
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Related Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($relatedArticles as $related)
                    <a href="{{ route('knowledgebase.show', $related->slug) }}" class="block bg-white rounded-lg shadow p-4 hover:bg-blue-50 transition">
                        <div class="font-semibold text-blue-700 mb-1">{{ $related->title }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($related->excerpt, 80) }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection 