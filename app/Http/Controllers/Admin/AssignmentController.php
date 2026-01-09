<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ChemicalStructure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function create()
    {
        return view('admin.assignments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string',
            'due_date' => 'required|date',
            'total_points' => 'required|integer|min:0',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            'structure_data' => 'nullable|string',
            'molecular_formula' => 'nullable|string',
            'molecular_weight' => 'nullable|numeric',
            'smiles' => 'nullable|string',
            'inchi' => 'nullable|string',
        ]);

        // Create assignment
        $assignment = Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'due_date' => $request->due_date,
            'total_points' => $request->total_points,
            'created_by' => auth()->id(),
        ]);

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('assignments', 'public');
            $assignment->update(['attachment_path' => $path]);
        }

        // Handle chemical structure if present
        if ($request->has('structure_data')) {
            ChemicalStructure::create([
                'assignment_id' => $assignment->id,
                'structure_data' => $request->structure_data,
                'molecular_formula' => $request->molecular_formula,
                'molecular_weight' => $request->molecular_weight,
                'smiles' => $request->smiles,
                'inchi' => $request->inchi,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment created successfully');
    }

    public function show(Assignment $assignment)
    {
        return view('admin.assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        return view('admin.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string',
            'due_date' => 'required|date',
            'total_points' => 'required|integer|min:0',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            'structure_data' => 'nullable|string',
            'molecular_formula' => 'nullable|string',
            'molecular_weight' => 'nullable|numeric',
            'smiles' => 'nullable|string',
            'inchi' => 'nullable|string',
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'due_date' => $request->due_date,
            'total_points' => $request->total_points,
        ]);

        // Handle attachment update
        if ($request->hasFile('attachment')) {
            if ($assignment->attachment_path) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }
            $path = $request->file('attachment')->store('assignments', 'public');
            $assignment->update(['attachment_path' => $path]);
        }

        // Handle chemical structure update
        if ($request->has('structure_data')) {
            $structure = $assignment->chemicalStructure ?? new ChemicalStructure();
            $structure->updateOrCreate(
                ['assignment_id' => $assignment->id],
                [
                    'structure_data' => $request->structure_data,
                    'molecular_formula' => $request->molecular_formula,
                    'molecular_weight' => $request->molecular_weight,
                    'smiles' => $request->smiles,
                    'inchi' => $request->inchi,
                    'created_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment updated successfully');
    }

    public function destroy(Assignment $assignment)
    {
        // Delete attachment if exists
        if ($assignment->attachment_path) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }

        // Delete chemical structure if exists
        $assignment->chemicalStructure?->delete();

        $assignment->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment deleted successfully');
    }

    public function exportXml(Assignment $assignment)
    {
        $structure = $assignment->chemicalStructure;
        if (!$structure) {
            abort(404, 'No chemical structure found for this assignment');
        }

        $xml = $structure->exportToXML();
        
        return response($xml)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="structure_' . $assignment->id . '.xml"');
    }

    public function importXml(Request $request, Assignment $assignment)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
        ]);

        $xml = file_get_contents($request->file('xml_file')->path());
        $structure = $assignment->chemicalStructure ?? new ChemicalStructure();
        
        if ($structure->importFromXML($xml)) {
            return redirect()->back()->with('success', 'Structure imported successfully');
        }

        return redirect()->back()->with('error', 'Failed to import structure');
    }
}
