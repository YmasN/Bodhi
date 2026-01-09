<?php

namespace App\Services;

use App\Models\ChemicalStructure;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class ChemicalStructureGradingService
{
    public function gradeStructure(Submission $submission, ChemicalStructure $answerKey)
    {
        $studentStructure = $submission->chemicalStructure;
        
        if (!$studentStructure) {
            return $this->createGrade($submission, 0, 'No structure submitted');
        }

        $score = 0;
        $totalPoints = $submission->assignment->total_points;
        $feedback = [];

        // Validate basic structure
        $basicValidation = $this->validateBasicStructure($studentStructure, $answerKey);
        if (!$basicValidation['valid']) {
            return $this->createGrade(
                $submission,
                0,
                "Basic structure validation failed: " . implode(', ', $basicValidation['errors'])
            );
        }

        // Grade molecular formula
        $formulaScore = $this->gradeMolecularFormula($studentStructure, $answerKey);
        $score += $formulaScore['score'];
        $feedback[] = $formulaScore['feedback'];

        // Grade functional groups
        $functionalScore = $this->gradeFunctionalGroups($studentStructure, $answerKey);
        $score += $functionalScore['score'];
        $feedback[] = $functionalScore['feedback'];

        // Grade bond types
        $bondScore = $this->gradeBondTypes($studentStructure, $answerKey);
        $score += $bondScore['score'];
        $feedback[] = $bondScore['feedback'];

        // Grade stereochemistry
        $stereoScore = $this->gradeStereochemistry($studentStructure, $answerKey);
        $score += $stereoScore['score'];
        $feedback[] = $stereoScore['feedback'];

        // Grade charge distribution
        $chargeScore = $this->gradeChargeDistribution($studentStructure, $answerKey);
        $score += $chargeScore['score'];
        $feedback[] = $chargeScore['feedback'];

        // Create final grade
        return $this->createGrade(
            $submission,
            $score,
            implode('\n', $feedback)
        );
    }

    private function validateBasicStructure($studentStructure, $answerKey)
    {
        $errors = [];

        // Check if structure is valid
        if (!$studentStructure->isValid()) {
            $errors[] = 'Invalid chemical structure';
        }

        // Check if structure is empty
        if ($studentStructure->isEmpty()) {
            $errors[] = 'Empty structure submitted';
        }

        // Check if structure type matches
        if ($studentStructure->structure_type !== $answerKey->structure_type) {
            $errors[] = 'Structure type mismatch';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    private function gradeMolecularFormula($studentStructure, $answerKey)
    {
        $maxPoints = 20;
        $points = 0;
        $feedback = [];

        if ($studentStructure->molecular_formula === $answerKey->molecular_formula) {
            $points = $maxPoints;
            $feedback[] = 'Molecular formula is correct';
        } else {
            $points = 0;
            $feedback[] = 'Molecular formula is incorrect';
        }

        return [
            'score' => $points,
            'feedback' => implode(' ', $feedback)
        ];
    }

    private function gradeFunctionalGroups($studentStructure, $answerKey)
    {
        $maxPoints = 30;
        $points = 0;
        $feedback = [];

        $studentGroups = $studentStructure->getFunctionalGroups();
        $answerGroups = $answerKey->getFunctionalGroups();

        $correctGroups = array_intersect($studentGroups, $answerGroups);
        $points = ($maxPoints * count($correctGroups)) / count($answerGroups);

        $feedback[] = sprintf(
            'Functional groups: %d/%d correct (%s)',
            count($correctGroups),
            count($answerGroups),
            implode(', ', $correctGroups)
        );

        return [
            'score' => $points,
            'feedback' => implode(' ', $feedback)
        ];
    }

    private function gradeBondTypes($studentStructure, $answerKey)
    {
        $maxPoints = 20;
        $points = 0;
        $feedback = [];

        $studentBonds = $studentStructure->getBondTypes();
        $answerBonds = $answerKey->getBondTypes();

        $correctBonds = array_intersect($studentBonds, $answerBonds);
        $points = ($maxPoints * count($correctBonds)) / count($answerBonds);

        $feedback[] = sprintf(
            'Bond types: %d/%d correct',
            count($correctBonds),
            count($answerBonds)
        );

        return [
            'score' => $points,
            'feedback' => implode(' ', $feedback)
        ];
    }

    private function gradeStereochemistry($studentStructure, $answerKey)
    {
        $maxPoints = 15;
        $points = 0;
        $feedback = [];

        if ($studentStructure->hasStereochemistry() && $answerKey->hasStereochemistry()) {
            $correct = $studentStructure->matchesStereochemistry($answerKey);
            $points = $correct ? $maxPoints : 0;
            $feedback[] = $correct ? 'Stereochemistry is correct' : 'Stereochemistry is incorrect';
        } else {
            $points = $maxPoints;
            $feedback[] = 'No stereochemistry required';
        }

        return [
            'score' => $points,
            'feedback' => implode(' ', $feedback)
        ];
    }

    private function gradeChargeDistribution($studentStructure, $answerKey)
    {
        $maxPoints = 15;
        $points = 0;
        $feedback = [];

        if ($studentStructure->total_charge === $answerKey->total_charge) {
            $points = $maxPoints;
            $feedback[] = 'Charge distribution is correct';
        } else {
            $points = 0;
            $feedback[] = sprintf(
                'Charge distribution incorrect: %d instead of %d',
                $studentStructure->total_charge,
                $answerKey->total_charge
            );
        }

        return [
            'score' => $points,
            'feedback' => implode(' ', $feedback)
        ];
    }

    private function createGrade(Submission $submission, $score, $feedback)
    {
        return Grade::create([
            'submission_id' => $submission->id,
            'points_earned' => $score,
            'feedback' => $feedback,
            'graded_by' => auth()->id(),
        ]);
    }
}
