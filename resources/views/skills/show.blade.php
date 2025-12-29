<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Skill Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('skills.edit', $skill) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('skills.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Skill Information</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $skill->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Employees</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $skill->employees->count() }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $skill->created_at->format('M d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $skill->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employees with this Skill</h3>
                            
                            @if ($skill->employees->count() > 0)
                                <div class="space-y-2">
                                    @foreach ($skill->employees as $employee)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $employee->email }} • {{ $employee->department->name }}</p>
                                            </div>
                                            <a href="{{ route('employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 italic">No employees have this skill.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                            <div class="space-x-2">
                                <a href="{{ route('skills.edit', $skill) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Edit Skill
                                </a>
                                @if ($skill->employees->count() == 0)
                                    <form action="{{ route('skills.destroy', $skill) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this skill?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Delete Skill
                                        </button>
                                    </form>
                                @else
                                    <span class="bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed" title="Cannot delete skill assigned to employees">
                                        Delete Skill
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>