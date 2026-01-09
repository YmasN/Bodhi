<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChemicalStructure extends Model
{
    protected $fillable = [
        'assignment_id',
        'structure_data',
        'structure_type',
        'molecular_formula',
        'molecular_weight',
        'smiles',
        'inchi',
        'created_by',
    ];

    protected $casts = [
        'structure_data' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validateStructure()
    {
        try {
            // Use RDKit for structure validation
            $rdkit = new \Chem\RDKit();
            $mol = $rdkit->fromSmiles($this->smiles);
            if (!$mol) {
                throw new \Exception('Invalid chemical structure');
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateImage()
    {
        try {
            // Generate structure image using RDKit
            $rdkit = new \Chem\RDKit();
            $mol = $rdkit->fromSmiles($this->smiles);
            $image = $rdkit->drawMolecule($mol);
            
            // Save image to storage
            $path = storage_path('app/public/structures/' . $this->id . '.png');
            file_put_contents($path, $image);
            
            return asset('storage/structures/' . $this->id . '.png');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function exportToXML()
    {
        $xml = new \SimpleXMLElement('<ChemicalStructure></ChemicalStructure>');
        
        $xml->addAttribute('id', $this->id);
        $xml->addAttribute('type', $this->structure_type);
        
        $xml->addChild('StructureData', json_encode($this->structure_data));
        $xml->addChild('MolecularFormula', $this->molecular_formula);
        $xml->addChild('MolecularWeight', $this->molecular_weight);
        $xml->addChild('Smiles', $this->smiles);
        $xml->addChild('InChI', $this->inchi);
        
        return $xml->asXML();
    }

    public function importFromXML($xml)
    {
        $data = simplexml_load_string($xml);
        
        $this->structure_data = json_decode((string)$data->StructureData, true);
        $this->molecular_formula = (string)$data->MolecularFormula;
        $this->molecular_weight = (float)$data->MolecularWeight;
        $this->smiles = (string)$data->Smiles;
        $this->inchi = (string)$data->InChI;
        
        return $this->save();
    }
}
