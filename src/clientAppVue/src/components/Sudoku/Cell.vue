<script setup lang="ts">

import {Cell} from "@/components/Sudoku/Cell.ts";
import type {CellGroup} from "@/components/Sudoku/CellGroup.ts";

const {cell, store} = defineProps(['cell', 'store'])

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

function getColor(row: number, col: number): string {
  // This math not based on row/col indexes but coords. The action "-1" is a part of the math
  return (!!((Math.floor((col - 1) / 3) + Math.floor((row - 1) / 3)) % 2)) ? '' : 'grey'
}

function getCellClasses(cell: Cell): string[] {
  const classes = []

  const [row, col] = store.puzzle.getRowColByCoords(cell.coords)
  const groupColor = getColor(row, col)
  const rowClass = 'row-' + row
  const colClass = 'col-' + col

  const protectedClass = cell.protected ? 'protected' : 'not-protected'

  const selected = store.selectedCell?.coords === cell.coords ? 'selected' : ''

  const hovered = store.hoveredCellGroups.some((cellGroup: CellGroup) => cellGroup.cells.has(cell.coords)) ? 'hovered' : ''

  const mistake = store.mistakes.has(cell.coords) ? 'mistake' : ''

  const highlightedValue = store.highlightedValue && cell.value === store.highlightedValue ? 'highlighted-value' : '';

  classes.push(
      groupColor,
      colClass,
      rowClass,
      protectedClass,
      selected,
      hovered,
      mistake,
      highlightedValue
  )
  return classes;
}

function cellClickHandler(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement
  const coords = currentTarget.getAttribute('data-coords') || '';
  store.setSelectedCell(coords)
  store.highlightValue(store.selectedCell.value)
}

function mouseoverHandler(event: MouseEvent) {
  const currentTarget = event.currentTarget as HTMLElement
  const hoveredCellCoords = currentTarget.getAttribute('data-coords') || ''
  store.hoverCell(hoveredCellCoords)
}

function mouseleaveHandler(event: MouseEvent) {
  const currentTarget = event.currentTarget as HTMLElement
  const leftCellCoords = currentTarget.getAttribute('data-coords') || ''
  store.leaveCell(leftCellCoords)
}

</script>

<template>
  <div class="cell"
       :data-coords="cell.coords"
       :class="getCellClasses(cell)"
       @click="cellClickHandler"
       @mouseover="mouseoverHandler"
       @mouseleave="mouseleaveHandler"
  >
    <div class="cell-value" v-show="cellDisplayState(cell as Cell) === 'value'">{{ cell.value }}</div>
    <div class="cell-notes" v-show="cellDisplayState(cell as Cell) === 'notes'">
      <div v-for="note in getCellNotes(cell as Cell)" class="cell-note">
        {{ note > 0 ? note : '' }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.cell {
  height: 50px;
  width: 50px;
  font-size: 40px;

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
</style>
