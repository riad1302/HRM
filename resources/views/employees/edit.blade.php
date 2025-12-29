<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Employee') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('employees.update', $employee) }}" method="POST" id="employee-form">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('first_name') border-red-500 @enderror" required>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('last_name') border-red-500 @enderror" required>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" required>
                                <div id="email-feedback" class="mt-1 text-sm"></div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select name="department_id" id="department_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('department_id') border-red-500 @enderror" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                            <div id="skills-container">
                                <div class="flex flex-wrap gap-2 mb-4" id="selected-skills">
                                    @foreach ($employee->skills as $skill)
                                        <div class="skill-tag inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                            <span>{{ $skill->name }}</span>
                                            <button type="button" class="ml-2 text-blue-600 hover:text-blue-800" onclick="removeSkill('{{ $skill->id }}')">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                            <input type="hidden" name="skills[]" value="{{ $skill->id }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mb-4">
                                    <select id="skill-select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select a skill to add</option>
                                        @foreach ($skills as $skill)
                                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('skills')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('employees.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let selectedSkills = @json($employee->skills->pluck('id')->toArray());

            // Email validation
            $('#email').on('blur', function() {
                const email = $(this).val();
                const currentEmail = '{{ $employee->email }}';
                
                if (email && email !== currentEmail) {
                    $.ajax({
                        url: '{{ route("check.email") }}',
                        type: 'POST',
                        data: {
                            email: email,
                            employee_id: '{{ $employee->id }}',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.exists) {
                                $('#email-feedback').html('<span class="text-red-600">This email is already taken.</span>');
                            } else {
                                $('#email-feedback').html('<span class="text-green-600">Email is available.</span>');
                            }
                        }
                    });
                } else {
                    $('#email-feedback').html('');
                }
            });

            // Skills management
            $('#skill-select').on('change', function() {
                const skillId = $(this).val();
                const skillName = $(this).find('option:selected').text();
                
                if (skillId && !selectedSkills.includes(skillId)) {
                    selectedSkills.push(skillId);
                    addSkillTag(skillId, skillName);
                    $(this).val('');
                }
            });

            function addSkillTag(skillId, skillName) {
                const skillTag = `
                    <div class="skill-tag inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                        <span>${skillName}</span>
                        <button type="button" class="ml-2 text-blue-600 hover:text-blue-800" onclick="removeSkill('${skillId}')">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <input type="hidden" name="skills[]" value="${skillId}">
                    </div>
                `;
                $('#selected-skills').append(skillTag);
            }

            window.removeSkill = function(skillId) {
                selectedSkills = selectedSkills.filter(id => id !== skillId);
                $(`input[value="${skillId}"]`).closest('.skill-tag').remove();
            };
        });
    </script>
    @endpush
</x-app-layout>