<script setup lang="ts">
import {ref, watch} from 'vue'
import {Table} from "./Table.ts";
import {CellGroupDto, MistakeDto, TableStateDto} from "./Dto.ts";
import {Cell} from "./Cell.ts";
import {CellGroup, CellGroupTypes} from "./CellGroup.ts";
import {defineStore} from "pinia";

const props = withDefaults(defineProps<{
  stateDto: TableStateDto,
}>(), {
  stateDto: () => ({cells: [], groups: [] as CellGroupDto[]})
});

interface SudokuStore {
  selectedCell: Cell | undefined
  hoveredCell: Cell | undefined
}

const useSudokuStore = defineStore('sudoku', {
  state(): SudokuStore {
    return {
      selectedCell: undefined as Cell | undefined,
      hoveredCell: undefined as Cell | undefined,
    }
  },
  getters: {
  },
  actions: {
  },
})
const store = useSudokuStore() as SudokuStore

const table = ref(new Table(props.stateDto))
const isNoteModeEnabled = ref(false)
let mistakes: Map<string, MistakeDto> = new Map<string, MistakeDto>()

watch( () => props.stateDto, (newVal) => {
  table.value = new Table(newVal)
  resetSelectedCell()
});

function getColor(row: number, col: number): string {
  return ( !!((Math.floor((col - 1) / 3) + Math.floor((row - 1) / 3)) % 2) ) ? '' : 'grey'
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

  const selected = store.selectedCell?.coords === cell.coords ? 'selected' : ''

  const highlightedClasses = getHighlightedClasses(cell)
  const hoveredClasses = getHoveredClasses(cell)

  let highlightedValue = '';
  if (store.selectedCell && store.selectedCell?.value > 0 && store.selectedCell?.value === cell.value) {
    highlightedValue = 'highlighted-value'
  }

  classes.push(
      groupColor,
      colClass,
      rowClass,
      protectedClass,
      mistake,
      selected,
      ...hoveredClasses,
      ...highlightedClasses,
      highlightedValue
  )
  return classes;
}

function getHighlightedClasses(cell: Cell): string[] {
  const highlightedClasses: string[] = [];

  if (!store.selectedCell) {
    return highlightedClasses
  }

  const groups: CellGroup[] = table.value.groups as CellGroup[]

  groups.forEach((cellGroup: any) => {
    if (cellGroup.cells.has(store.selectedCell?.coords)) {
      if (cellGroup.cells.has((cell.coords))) {
        if (cellGroup.type === CellGroupTypes.ROW || cellGroup.type === CellGroupTypes.COL) {
          highlightedClasses.push('highlighted')
        }
      }
    }
  })

  return highlightedClasses
}

function getHoveredClasses(cell: Cell): string[] {
  const hoveredClasses: string[] = [];

  if (!store.hoveredCell) {
    return hoveredClasses
  }

  const groups: CellGroup[] = table.value.groups as CellGroup[]

  groups.forEach((cellGroup: any) => {
    if (cellGroup.cells.has(store.hoveredCell?.coords)) {
      if (cellGroup.cells.has((cell.coords))) {
        if (cellGroup.type === CellGroupTypes.ROW || cellGroup.type === CellGroupTypes.COL) {
          hoveredClasses.push('hovered')
        }
      }
    }
  })

  return hoveredClasses
}

function cellClickHandler(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement
  const selectedCellCoords = currentTarget.getAttribute('data-coords') || '';
  setSelectedCell(selectedCellCoords)
}

function setSelectedCell(selectedCellCoords: string) {
  const coordsArray: string[] = selectedCellCoords.split(":");
  if (selectedCellCoords === '' || store.selectedCell?.coords === selectedCellCoords) {
    resetSelectedCell()
  } else {
    const row: number = parseInt(coordsArray[0]);
    const col: number = parseInt(coordsArray[1]);
    store.selectedCell = table.value.cells[row - 1][col - 1] as Cell || undefined
  }
}

function resetSelectedCell() {
  store.selectedCell = undefined
}

function handleKeyup(event: KeyboardEvent) {
  const key = +event.key
  if (store.selectedCell && !store.selectedCell?.protected) {
    if (isNoteModeEnabled.value) {
      store.selectedCell.hasNote(key) ? store.selectedCell.deleteNote(key) : store.selectedCell.addNote(key)
    } else {
      if (store.selectedCell.value === key) {
        store.selectedCell.deleteValue()
      } else {
        store.selectedCell.value = key
        table.value.cleanNotesByCellValue(store.selectedCell as Cell)
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

function mouseover(event: MouseEvent) {
  const currentTarget = event.currentTarget as HTMLElement
  const hoveredCellCoords = currentTarget.getAttribute('data-coords') || ''
  const coordsArray: string[] = hoveredCellCoords.split(":");
  const row: number = parseInt(coordsArray[0]);
  const col: number = parseInt(coordsArray[1]);
  store.hoveredCell = table.value.cells[row - 1][col - 1] as Cell || undefined
}

function mouseleave(event: MouseEvent) {
  if (!store.hoveredCell) {
    return
  }
  const currentTarget = event.currentTarget as HTMLElement
  const hoveredCellCoords = currentTarget.getAttribute('data-coords') || ''
  if (hoveredCellCoords === store.hoveredCell.coords) {
    store.hoveredCell = undefined
  }
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
          :data-coords="cell.coords"
          @click="cellClickHandler"
          @keyup="handleKeyup"
          @mouseover="mouseover"
          @mouseleave="mouseleave"
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

      &.highlighted {
        background-color: darkgray;
      }

      &.hovered {
        background-color: #c2c2c2;
      }

      &.highlighted-value {
        background-color: #6e6e6e;
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
