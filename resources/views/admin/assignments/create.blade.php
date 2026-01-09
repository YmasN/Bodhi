<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Assignment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" id="title" 
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="description" id="description" rows="3"
                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                        <select name="subject" id="subject" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="chemistry">Chemistry</option>
                                            <option value="math">Math</option>
                                            <option value="physics">Physics</option>
                                            <option value="biology">Biology</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                        <input type="datetime-local" name="due_date" id="due_date" 
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Chemical Structure Editor (for Chemistry assignments) -->
                            <div id="chemical-editor" class="hidden">
                                <h3 class="text-lg font-medium text-gray-900">Chemical Structure</h3>
                                <div class="mt-4">
                                    <chemical-structure-editor 
                                        @save="saveStructure"
                                        :initial-structure="initialStructure">
                                    </chemical-structure-editor>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Attachment</h3>
                                <div class="mt-4">
                                    <label for="attachment" class="block text-sm font-medium text-gray-700">Upload Assignment File</label>
                                    <input type="file" name="attachment" id="attachment" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <!-- Points -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Grading</h3>
                                <div class="mt-4">
                                    <label for="total_points" class="block text-sm font-medium text-gray-700">Total Points</label>
                                    <input type="number" name="total_points" id="total_points" min="0"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Assignment
                            </button>
                            <a href="{{ route('assignments.index') }}" 
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
    // Show/hide chemical editor based on subject selection
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject');
        const chemicalEditor = document.getElementById('chemical-editor');
        
        subjectSelect.addEventListener('change', function() {
            if (this.value === 'chemistry') {
                chemicalEditor.classList.remove('hidden');
            } else {
                chemicalEditor.classList.add('hidden');
            }
        });
    });

    // Handle structure save event
    window.saveStructure = function(data) {
        // Add structure data to form
        const formData = new FormData();
        formData.append('structure_data', data.structure_data);
        formData.append('molecular_formula', data.molecular_formula);
        formData.append('molecular_weight', data.molecular_weight);
        formData.append('smiles', data.smiles);
        formData.append('inchi', data.inchi);
    };
</script>
