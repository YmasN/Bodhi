<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Assignment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

                        <div class="space-y-6">
                            <!-- Assignment Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Assignment Details</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $assignment->title }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $assignment->description }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $assignment->due_date->format('F j, Y, g:i A') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Points</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $assignment->total_points }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Chemical Structure Editor (for Chemistry assignments) -->
                            @if($assignment->subject === 'chemistry')
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Chemical Structure</h3>
                                <div class="mt-4">
                                    <chemical-structure-editor 
                                        @save="saveStructure"
                                        :initial-structure="initialStructure">
                                    </chemical-structure-editor>
                                </div>
                            </div>
                            @endif

                            <!-- File Upload -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Attachment</h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Upload Solution</label>
                                    <input type="file" name="attachment" id="attachment" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Additional Notes</h3>
                                <div class="mt-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Submit Assignment
                            </button>
                            <a href="{{ route('assignments.show', $assignment) }}" 
                               class="ml-4 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Handle structure save event
    window.saveStructure = function(data) {
        // Add structure data to form
        const formData = new FormData();
        formData.append('structure_data', data.structure_data);
        formData.append('molecular_formula', data.molecular_formula);
        formData.append('molecular_weight', data.molecular_weight);
        formData.append('smiles', data.smiles);
        formData.append('inchi', data.inchi);
        formData.append('total_charge', data.total_charge);
        formData.append('functional_groups', JSON.stringify(data.functional_groups));
    };
</script>
