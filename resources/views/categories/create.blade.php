@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Create New Category</h2>

        @if (session('success'))
            <div class="p-4 rounded mb-6 bg-green-50 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded mb-6 bg-red-50 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="form-label">Category Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="form-input @error('name') border-red-500 @enderror"
                    placeholder="Enter category name"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Category -->
            <div>
                <label for="parent_id" class="form-label">Parent Category (Optional)</label>
                <select
                    id="parent_id"
                    name="parent_id"
                    class="form-input @error('parent_id') border-red-500 @enderror"
                >
                    <option value="">None (Root Category)</option>
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            {{ old('parent_id') == $category->id ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>
                        @foreach($category->children as $child)
                            <option
                                value="{{ $child->id }}"
                                {{ old('parent_id') == $child->id ? 'selected' : '' }}
                            >
                                — {{ $child->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Create Category
                </button>
            </div>
        </form>

        <!-- Category Tree Preview -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Existing Categories</h3>
            <div class="space-y-2">
                @foreach($categories as $category)
                    <div class="font-medium">{{ $category->name }}</div>
                    @foreach($category->children as $child)
                        <div class="ml-4 text-gray-600">
                            — {{ $child->name }}
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
