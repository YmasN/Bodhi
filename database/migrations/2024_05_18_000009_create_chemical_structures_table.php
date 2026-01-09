<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChemicalStructuresTable extends Migration
{
    public function up()
    {
        Schema::create('chemical_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->json('structure_data');
            $table->string('structure_type');
            $table->string('molecular_formula')->nullable();
            $table->decimal('molecular_weight', 10, 4)->nullable();
            $table->string('smiles')->nullable();
            $table->string('inchi')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chemical_structures');
    }
}
