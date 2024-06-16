<script setup lang="ts">
import {reactive, ref, watch} from 'vue'
import {Table} from "./Table.ts";
import {TableStateDto} from "./Dto.ts";
import {Cell} from "./Cell.ts";

const props = withDefaults(defineProps<{
  stateDto: TableStateDto,
}>(), {
  stateDto: () => ({cells: []})
});

const table = ref(new Table(props.stateDto))
const selectedCell = reactive({id: null, cell: null})
const isNoteModeEnabled = ref(false)

watch( () => props.stateDto, (newVal) => {
  table.value = new Table(newVal)
  resetSelectedCell()
});

function getColor(col: number, row: number): boolean {
  return !!((Math.floor((col - 1) / 3) + Math.floor((row - 1) / 3)) % 2)
}

function getCellId(cell: Cell): string {
  return '' + cell.coords.row + '-' + cell.coords.col;
}

function getCellClasses(cell: Cell): string[] {
  const classes = []

  const groupColor = getColor(cell.coords.col, cell.coords.row) ? '' : 'grey'
  const colClass = 'col-' + cell.coords.col
  const rowClass = 'row-' + cell.coords.row
  const protectedClass = cell.protected ? 'protected' : 'not-protected'

  const cellId = getCellId(cell)
  const selected = selectedCell.id === cellId ? 'selected' : ''

  classes.push(groupColor, colClass, rowClass, selected, protectedClass)
  return classes;
}

function cellClickHandler(event) {
  setSelectedCell(
      event.target.id,
      event.target.getAttribute('data-row'),
      event.target.getAttribute('data-col')
  )
}

function setSelectedCell(selectedCellId: string, selectedCellRow: number, selectedCellCol: number) {
  if (selectedCell.id === selectedCellId) {
    resetSelectedCell()
  } else {
    selectedCell.id = selectedCellId
    selectedCell.cell = table.value.cells[selectedCellRow - 1][selectedCellCol - 1]
  }
}

function resetSelectedCell() {
  selectedCell.id = null
  selectedCell.cell = null
}

function handleKeyup(event) {
  const key = event.key
  if (selectedCell.id && !selectedCell.cell.protected) {
    if (key >= '0' && key <= '9') {
      if (isNoteModeEnabled.value) {
        selectedCell.cell.hasNote(key) ? selectedCell.cell.deleteNote(key) : selectedCell.cell.addNote(key)
      } else {
        if (selectedCell.cell.value === key) {
          selectedCell.cell.value = 0
        } else {
          selectedCell.cell.value = key
          table.value.validateSolution()
        }
      }
    }
  }
}

function toggleNoteMode() {
  isNoteModeEnabled.value = !isNoteModeEnabled.value
}

function cellDisplayState(cell: Cell): string {
  if (cell.value > 0) {
    return 'value'
  } else if (cell.getNotes().length > 0) {
    return 'notes'
  } else {
    return ''
  }
}

function getCellNotes(cell: Cell): number[] {
  const allNotes = Array.from({length: 9}, () => 0)
  cell.getNotes().forEach((note: number) => {
    allNotes[note - 1] = note
  })

  return allNotes

  // return Array.from({length: Math.ceil(allNotes.length / 3)}, (v, i) => allNotes.slice(i * 3, i * 3 + 3))
}

</script>

<template>
<div class="sudoku-table">
  <div v-if="table.isSolved">
    SOLVED!
  </div>
  <button @click="$emit('newGameEvent')">New game?</button><br>
  <button @click="toggleNoteMode">&nbsp;</button> Note mode is {{ isNoteModeEnabled ? 'enabled' : 'disabled' }}
  <table>
    <tr v-for="row in table.cells">
      <td v-for="cell in row"
          :id="getCellId(cell as Cell)"
          :class="getCellClasses(cell as Cell)"
          :data-row="cell.coords.row"
          :data-col="cell.coords.col"
          @click="cellClickHandler"
          @keyup="handleKeyup"
          tabindex="0"
      >
        <div class="cell-value" v-show="cellDisplayState(cell) === 'value'">{{ cell.value }}</div>
        <div class="cell-notes" v-show="cellDisplayState(cell) === 'notes'">
          <div v-for="note in getCellNotes(cell)" class="cell-note">
            {{ note > 0 ? note : '' }}
          </div>
        </div>

      </td>
    </tr>
  </table>
</div>
</template>

<style scoped>
.sudoku-table {
  text-align: center;

  > table {
    border-spacing: 0;
    border-width: 0 1px 1px 0;
    border-style: solid;
    border-color: #000;
    margin: auto;

    > tr > td {
      border-width: 1px 0 0 1px;
      border-style: solid;
      border-color: #000;
      width: 20px;
      height: 20px;
      text-align: center;
      vertical-align: middle;

      &.grey {
        background-color: lightgray;
      }

      &:hover {
        background-color: grey;
      }

      &.selected {
        background-color: dimgray;
      }

      &.not-protected {
        font-weight: bold;
        font-style: italic;
      }

      .cell-value {
        padding: 5px;
      }
    }
  }

  .cell-notes {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(3, 1fr);

    width: 20px;
    max-width: 20px;
    height: 20px;
    max-height: 20px;
    font-size: 10px;

    padding: 0;

    .cell-note {
      margin-top: -5px;
    }

    table.cell-notes-table {
      width: 20px;
      max-width: 20px;
      height: 20px;
      max-height: 20px;
      font-size: 5px;
    }
  }

}
</style>
