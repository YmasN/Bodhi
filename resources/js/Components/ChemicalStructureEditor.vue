<template>
  <div class="chemical-structure-editor">
    <!-- Element Palette -->
    <div class="element-palette mb-4">
      <div class="element-group" v-for="(group, groupId) in elementGroups" :key="groupId">
        <h4 class="group-title">{{ group }}</h4>
        <div class="element-buttons">
          <button v-for="(element, symbol) in elements" 
                  :key="symbol"
                  v-if="elements[symbol].group === parseInt(groupId)"
                  @click="addAtom(symbol)"
                  class="element-btn"
                  :class="{ 'active': selectedElement === symbol }"
                  @mouseover="showElementInfo(symbol)"
                  @mouseout="hideElementInfo">
            {{ symbol }}
          </button>
        </div>
      </div>
    </div>

    <!-- Bond Types -->
    <div class="bond-types mb-4">
      <button @click="addBond(1)" class="bond-btn" :class="{ 'active': selectedBond === 1 }">
        Single Bond
      </button>
      <button @click="addBond(2)" class="bond-btn" :class="{ 'active': selectedBond === 2 }">
        Double Bond
      </button>
      <button @click="addBond(3)" class="bond-btn" :class="{ 'active': selectedBond === 3 }">
        Triple Bond
      </button>
      <button @click="addBond(4)" class="bond-btn" :class="{ 'active': selectedBond === 4 }">
        Aromatic Ring
      </button>
    </div>

    <!-- Structure Canvas -->
    <div class="structure-canvas mb-4" ref="structureCanvas">
      <!-- Canvas for structure drawing -->
    </div>

    <!-- Tools Panel -->
    <div class="tools-panel mb-4">
      <div class="tool-group">
        <button @click="selectTool('select')" :class="{ 'active': currentTool === 'select' }">
          Select
        </button>
        <button @click="selectTool('move')" :class="{ 'active': currentTool === 'move' }">
          Move
        </button>
        <button @click="selectTool('erase')" :class="{ 'active': currentTool === 'erase' }">
          Erase
        </button>
        <button @click="selectTool('rotate')" :class="{ 'active': currentTool === 'rotate' }">
          Rotate
        </button>
      </div>

      <!-- Advanced Features -->
      <div class="advanced-features">
        <button @click="addRing(5)" class="feature-btn">
          5-member Ring
        </button>
        <button @click="addRing(6)" class="feature-btn">
          6-member Ring
        </button>
        <button @click="addFunctionalGroup" class="feature-btn">
          Add Functional Group
        </button>
        <button @click="addCharge" class="feature-btn">
          Add Charge
        </button>
      </div>
    </div>

    <!-- Structure Controls -->
    <div class="structure-controls">
      <div class="structure-info">
        <p>Molecular Formula: {{ molecularFormula }}</p>
        <p>Molecular Weight: {{ molecularWeight }}</p>
        <p>SMILES: {{ smiles }}</p>
        <p>InChI: {{ inchi }}</p>
        <p>Charge: {{ totalCharge }}</p>
        <p>Functional Groups: {{ functionalGroups.join(', ') }}</p>
      </div>

      <div class="action-buttons">
        <button @click="exportStructure" class="btn btn-sm btn-success mr-2">
          Export XML
        </button>
        <button @click="importStructure" class="btn btn-sm btn-info mr-2">
          Import XML
        </button>
        <button @click="validateStructure" class="btn btn-sm btn-primary mr-2">
          Validate
        </button>
        <button @click="saveStructure" class="btn btn-sm btn-primary">
          Save
        </button>
        <button @click="clearStructure" class="btn btn-sm btn-danger">
          Clear
        </button>
      </div>
    </div>

    <!-- Import XML Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Import Structure</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <textarea v-model="importXml" class="form-control" rows="10"></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Cancel
            </button>
            <button type="button" class="btn btn-primary" @click="processImport">
              Import
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Element Info Tooltip -->
    <div v-if="showElementInfoTooltip" 
         class="element-info-tooltip"
         :style="{ left: tooltipPosition.x + 'px', top: tooltipPosition.y + 'px' }">
      <h5>{{ currentElementInfo.name }}</h5>
      <p>Atomic Number: {{ currentElementInfo.atomicNumber }}</p>
      <p>Group: {{ elementGroups[currentElementInfo.group] }}</p>
    </div>
  </div>
</template>

<script>
import * as Kekule from 'kekule';
import * as KekuleUI from 'kekule-ui';
import { ELEMENTS, ELEMENT_GROUPS } from '../data/chemical-elements';

export default {
  name: 'ChemicalStructureEditor',
  props: {
    initialStructure: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      editor: null,
      elements: ELEMENTS,
      elementGroups: ELEMENT_GROUPS,
      selectedElement: 'C',
      selectedBond: 1,
      currentTool: 'select',
      importXml: '',
      molecularFormula: '',
      molecularWeight: '',
      smiles: '',
      inchi: '',
      totalCharge: 0,
      functionalGroups: [],
      showElementInfoTooltip: false,
      tooltipPosition: { x: 0, y: 0 },
      currentElementInfo: {}
    };
  },
  mounted() {
    this.initializeEditor();
  },
  methods: {
    initializeEditor() {
      // Initialize Kekule.js editor
      this.editor = new KekuleUI.StructureEditor(this.$refs.structureCanvas);
      
      // Load initial structure if provided
      if (this.initialStructure) {
        this.editor.loadStructure(this.initialStructure);
      }

      // Set up event listeners
      this.editor.on('structureChanged', () => {
        this.updateStructureInfo();
      });
    },
    addAtom(symbol) {
      this.selectedElement = symbol;
      this.editor.addAtom(symbol);
    },
    addBond(order) {
      this.selectedBond = order;
      this.editor.addBond(order);
    },
    addRing(size) {
      this.editor.addRing(size);
    },
    addFunctionalGroup() {
      // Show functional group selection modal
      const groups = ['OH', 'NH2', 'COOH', 'CHO', 'CN'];
      // Implementation of functional group addition
    },
    addCharge() {
      this.editor.addCharge();
    },
    selectTool(tool) {
      this.currentTool = tool;
      this.editor.setTool(tool);
    },
    clearStructure() {
      if (confirm('Are you sure you want to clear the structure?')) {
        this.editor.clear();
      }
    },
    updateStructureInfo() {
      const structure = this.editor.getStructure();
      this.molecularFormula = Kekule.StructureUtils.getMolecularFormula(structure);
      this.molecularWeight = Kekule.StructureUtils.getMolecularWeight(structure);
      this.smiles = Kekule.StructureUtils.getCanonicalSmiles(structure);
      this.inchi = Kekule.StructureUtils.getInchi(structure);
      this.totalCharge = Kekule.StructureUtils.getTotalCharge(structure);
      this.functionalGroups = Kekule.StructureUtils.getFunctionalGroups(structure);
    },
    exportStructure() {
      const structure = this.editor.getStructure();
      const xml = Kekule.StructureUtils.toXml(structure);
      const blob = new Blob([xml], { type: 'application/xml' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'structure.xml';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    },
    importStructure() {
      $('#importModal').modal('show');
    },
    processImport() {
      try {
        const structure = Kekule.StructureUtils.fromXml(this.importXml);
        this.editor.loadStructure(structure);
        this.updateStructureInfo();
        $('#importModal').modal('hide');
      } catch (error) {
        alert('Invalid XML structure: ' + error.message);
      }
    },
    validateStructure() {
      const structure = this.editor.getStructure();
      try {
        const validation = Kekule.StructureUtils.validate(structure);
        if (validation.isValid) {
          alert('Structure is valid!');
        } else {
          alert(`Validation errors:\n${validation.errors.join('\n')}`);
        }
      } catch (error) {
        alert('Validation error: ' + error.message);
      }
    },
    saveStructure() {
      const structure = this.editor.getStructure();
      const data = {
        structure_data: Kekule.StructureUtils.toXml(structure),
        molecular_formula: this.molecularFormula,
        molecular_weight: this.molecularWeight,
        smiles: this.smiles,
        inchi: this.inchi,
        total_charge: this.totalCharge,
        functional_groups: this.functionalGroups
      };
      this.$emit('save', data);
    },
    showElementInfo(symbol) {
      this.currentElementInfo = this.elements[symbol];
      this.showElementInfoTooltip = true;
      this.updateTooltipPosition(event);
    },
    hideElementInfo() {
      this.showElementInfoTooltip = false;
    },
    updateTooltipPosition(event) {
      const rect = event.target.getBoundingClientRect();
      this.tooltipPosition = {
        x: rect.left + rect.width / 2,
        y: rect.bottom + 10
      };
    }
  }
};
</script>

<style scoped>
.chemical-structure-editor {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
}

.element-palette {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.element-group {
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.group-title {
  font-size: 14px;
  margin-bottom: 10px;
  color: #666;
}

.element-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.element-btn {
  padding: 5px 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.element-btn:hover {
  background: #f0f0f0;
}

.element-btn.active {
  background: #007bff;
  color: white;
}

.bond-types {
  display: flex;
  gap: 10px;
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.bond-btn {
  padding: 8px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.bond-btn.active {
  background: #007bff;
  color: white;
}

.structure-canvas {
  height: 500px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  position: relative;
}

.tools-panel {
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tool-group {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

.feature-btn {
  padding: 8px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.feature-btn:hover {
  background: #f0f0f0;
}

.structure-controls {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 20px;
}

.structure-info {
  background: white;
  padding: 15px;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.element-info-tooltip {
  position: absolute;
  background: white;
  padding: 10px;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  z-index: 1000;
}
</style>
