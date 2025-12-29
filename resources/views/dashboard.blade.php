<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HRM Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome to the HR Management System</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-2">Employees</h4>
                            <p class="text-blue-600 mb-4">Manage employee records, departments, and skills</p>
                            <a href="{{ route('employees.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View Employees</a>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">Departments</h4>
                            <p class="text-green-600 mb-4">Organize and manage company departments</p>
                            <a href="{{ route('departments.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">View Departments</a>
                        </div>
                        <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                            <h4 class="font-semibold text-purple-800 mb-2">Skills</h4>
                            <p class="text-purple-600 mb-4">Define and track employee skill sets</p>
                            <a href="{{ route('skills.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">View Skills</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
