<script setup lang="ts">
import {reactive, ref, watch} from 'vue'
import {Table} from "./Table.ts";
import {CellGroupDto, MistakeDto, TableStateDto} from "./Dto.ts";
import {Cell} from "./Cell.ts";

const props = withDefaults(defineProps<{
  stateDto: TableStateDto,
}>(), {
  stateDto: () => ({cells: [], groups: [] as CellGroupDto[]})
});

const table = ref(new Table(props.stateDto))
const selectedCell = reactive({id: '', cell: <Cell | undefined>undefined})
const isNoteModeEnabled = ref(false)
let mistakes: Map<string, MistakeDto> = new Map<string, MistakeDto>()

watch( () => props.stateDto, (newVal) => {
  table.value = new Table(newVal)
  resetSelectedCell()
});

function getColor(row: number, col: number): string {
  return ( !!((Math.floor((col - 1) / 3) + Math.floor((row - 1) / 3)) % 2) ) ? '' : 'grey'
}

function getCellId(cell: Cell): string {
  return cell.coords;
}

function getCellClasses(cell: Cell): string[] {
  const classes = []

  const coordsArray: string[] = cell.coords.split(":");
  const row: number = parseInt(coordsArray[0]);
  const col: number = parseInt(coordsArray[1]);

  const groupColor = getColor(row, col)
  const rowClass = 'row-' + row
  const colClass = 'col-' + col
  const protectedClass = cell.protected ? 'protected' : 'not-protected'
  const mistake = mistakes.has(cell.coords) ? 'mistake' : ''

  const cellId = getCellId(cell)
  const selected = selectedCell.id === cellId ? 'selected' : ''

  classes.push(groupColor, colClass, rowClass, protectedClass, mistake, selected)
  return classes;
}

function cellClickHandler(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement
  const selectedCellId = currentTarget.getAttribute('data-cell-id') || ''
  const selectedCellCoords = currentTarget.getAttribute('data-coords') || '';
  setSelectedCell(
      selectedCellId,
      selectedCellCoords
  )
}

function setSelectedCell(selectedCellId: string, selectedCellCoords: string) {
  const coordsArray: string[] = selectedCellCoords.split(":");
  const row: number = parseInt(coordsArray[0]);
  const col: number = parseInt(coordsArray[1]);

  if (selectedCellId === '' || selectedCell.id === selectedCellId) {
    resetSelectedCell()
  } else {
    selectedCell.id = selectedCellId
    selectedCell.cell = table.value.cells[row - 1][col - 1] as Cell || undefined
  }
}

function resetSelectedCell() {
  selectedCell.id = ''
  selectedCell.cell = undefined
}

function handleKeyup(event: KeyboardEvent) {
  const key = +event.key
  if (selectedCell.cell && !selectedCell.cell.protected) {
    if (isNoteModeEnabled.value) {
      selectedCell.cell.hasNote(key) ? selectedCell.cell.deleteNote(key) : selectedCell.cell.addNote(key)
    } else {
      if (selectedCell.cell.value === key) {
        selectedCell.cell.deleteValue()
      } else {
        selectedCell.cell.value = key
        table.value.cleanNotesByCellValue(selectedCell.cell as Cell)
        table.value.validateSolution()
      }
      checkMistakes(table.value as Table)
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
}

function checkMistakes(table: Table){
  mistakes = new Map<string, MistakeDto>()
  table.groups.forEach(group => {
    const seenValues = new Map<number, string>();
    group.cells.forEach((cell, coords) => {
      const cellValue = cell.value || 0;
      if (seenValues.has(cellValue)) {
        mistakes.set(coords, {cellCoords: coords})
        mistakes.set(seenValues.get(cellValue) as string, {cellCoords: seenValues.get(cellValue) as string})
      } else {
        seenValues.set(cellValue, coords)
      }
    });
  });
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
          :class="getCellClasses(cell as Cell)"
          :data-cell-id="getCellId(cell as Cell)"
          :data-coords="cell.coords"
          @click="cellClickHandler"
          @keyup="handleKeyup"
          tabindex="0"
      >
        <div class="cell-value" v-show="cellDisplayState(cell as Cell) === 'value'">{{ cell.value }}</div>
        <div class="cell-notes" v-show="cellDisplayState(cell as Cell) === 'notes'">
          <div v-for="note in getCellNotes(cell as Cell)" class="cell-note">
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

      &.mistake {
        color: darkred;
        background-color: lightcoral;
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
