<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grade Submission') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Student Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900">Student Information</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Student Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $submission->student->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Submission Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $submission->created_at->format('F j, Y, g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900">Assignment Details</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $submission->assignment->title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $submission->assignment->description }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Points</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $submission->assignment->total_points }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Student Submission -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900">Student Submission</h3>
                        <div class="mt-4 space-y-4">
                            <!-- Chemical Structure (if chemistry assignment) -->
                            @if($submission->assignment->subject === 'chemistry')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900">Chemical Structure</h4>
                                <div class="mt-2">
                                    <chemical-structure-viewer 
                                        :structure-data="JSON.parse('{{ json_encode($submission->chemicalStructure->structure_data) }}')">
                                    </chemical-structure-viewer>
                                </div>
                            </div>
                            @endif

                            <!-- Uploaded File -->
                            @if($submission->attachment_path)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900">Uploaded Solution</h4>
                                <div class="mt-2">
                                    <a href="{{ Storage::url($submission->attachment_path) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        View Solution
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Student Notes -->
                            @if($submission->notes)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900">Student Notes</h4>
                                <div class="mt-2">
                                    <p class="text-gray-700">{{ $submission->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Answer Key -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900">Answer Key</h3>
                        <div class="mt-4 space-y-4">
                            <!-- Chemical Structure (if chemistry assignment) -->
                            @if($submission->assignment->subject === 'chemistry')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900">Correct Structure</h4>
                                <div class="mt-2">
                                    <chemical-structure-viewer 
                                        :structure-data="JSON.parse('{{ json_encode($submission->assignment->chemicalStructure->structure_data) }}')">
                                    </chemical-structure-viewer>
                                </div>
                            </div>
                            @endif

                            <!-- Grading Breakdown -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900">Grading Breakdown</h4>
                                <div class="mt-2 space-y-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Molecular Formula</label>
                                        <p class="text-sm text-gray-900">
                                            {{ $grade->breakdown->molecular_formula->score }} / {{ $grade->breakdown->molecular_formula->max_score }}
                                            <span class="text-sm text-gray-600">
                                                ({{ $grade->breakdown->molecular_formula->feedback }})
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Functional Groups</label>
                                        <p class="text-sm text-gray-900">
                                            {{ $grade->breakdown->functional_groups->score }} / {{ $grade->breakdown->functional_groups->max_score }}
                                            <span class="text-sm text-gray-600">
                                                ({{ $grade->breakdown->functional_groups->feedback }})
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bond Types</label>
                                        <p class="text-sm text-gray-900">
                                            {{ $grade->breakdown->bond_types->score }} / {{ $grade->breakdown->bond_types->max_score }}
                                            <span class="text-sm text-gray-600">
                                                ({{ $grade->breakdown->bond_types->feedback }})
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Stereochemistry</label>
                                        <p class="text-sm text-gray-900">
                                            {{ $grade->breakdown->stereochemistry->score }} / {{ $grade->breakdown->stereochemistry->max_score }}
                                            <span class="text-sm text-gray-600">
                                                ({{ $grade->breakdown->stereochemistry->feedback }})
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Charge Distribution</label>
                                        <p class="text-sm text-gray-900">
                                            {{ $grade->breakdown->charge_distribution->score }} / {{ $grade->breakdown->charge_distribution->max_score }}
                                            <span class="text-sm text-gray-600">
                                                ({{ $grade->breakdown->charge_distribution->feedback }})
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Grade -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900">Final Grade</h3>
                        <div class="mt-4 space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-bold text-gray-900">
                                            {{ $grade->points_earned }} / {{ $submission->assignment->total_points }}
                                        </h4>
                                        <p class="mt-1 text-sm text-gray-700">
                                            {{ number_format(($grade->points_earned / $submission->assignment->total_points) * 100, 2) }}%
                                        </p>
                                    </div>
                                    <div class="text-4xl font-bold text-{{ $grade->points_earned >= 90 ? 'green' : ($grade->points_earned >= 70 ? 'yellow' : 'red') }}-600">
                                        {{ gradeLetter($grade->points_earned, $submission->assignment->total_points) }}
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900">Instructor Feedback</h4>
                                <div class="mt-2">
                                    <p class="text-gray-700">{{ $grade->feedback }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6">
                        <a href="{{ route('assignments.show', $submission->assignment) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Back to Assignment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Helper function to calculate letter grade
    function gradeLetter(pointsEarned, totalPoints) {
        const percentage = (pointsEarned / totalPoints) * 100;
        if (percentage >= 90) return 'A';
        if (percentage >= 80) return 'B';
        if (percentage >= 70) return 'C';
        if (percentage >= 60) return 'D';
        return 'F';
    }
</script>
