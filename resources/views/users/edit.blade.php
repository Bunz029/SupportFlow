@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit User: {{ $user->name }}</h1>
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-input mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-input mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-gray-700 font-semibold mb-1">Role</label>
                <select name="role" id="role" class="form-select mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>Agent</option>
                    <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-semibold mb-1">Status</label>
                <select name="status" id="status" class="form-select mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="active" {{ ($user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($user->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="job_title" class="block text-gray-700 font-semibold mb-1">Job Title</label>
                <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $user->job_title) }}" class="form-input mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="department" class="block text-gray-700 font-semibold mb-1">Department</label>
                <input type="text" name="department" id="department" value="{{ old('department', $user->department) }}" class="form-input mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="company" class="block text-gray-700 font-semibold mb-1">Company</label>
                <input type="text" name="company" id="company" value="{{ old('company', $user->company) }}" class="form-input mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-semibold mb-1">Password <span class="text-xs text-gray-400">(leave blank to keep current)</span></label>
                <input type="password" name="password" id="password" class="form-input mt-1 block w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold text-lg shadow hover:bg-blue-700 transition flex items-center justify-center gap-2">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection 