<script setup lang="ts">
import { ref, watch, reactive } from 'vue'
import { Table } from "./Table.ts";
import {TableStateDto} from "./Dto.ts";
import {Cell} from "./Cell.ts";

const props = withDefaults(defineProps<{
  stateDto: TableStateDto,
}>(), {
  stateDto: () => ({cells: []})
});

const table = ref(new Table(props.stateDto))
const selectedCell = reactive({id: null, cell: null})
let tableSolved = false

watch( () => props.stateDto, (newVal) => {
  table.value = new Table(newVal)
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
    selectedCell.id = null
    selectedCell.cell = null
  } else {
    selectedCell.id = selectedCellId
    selectedCell.cell = table.value.cells[selectedCellRow - 1][selectedCellCol - 1]
  }
}

function handleKeyup(event) {
  const key = event.key
  if (selectedCell.id && !selectedCell.cell.protected) {
    if (key >= '0' && key <= '9') {
      selectedCell.cell.value = key
      tableSolved = table.value.validateSolution()
    }
  }
}

</script>

<template>
<div class="sudoku-table">
  <div v-if="tableSolved">
    SOLVED!
  </div>
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
        {{cell.value > 0 ? cell.value : ''}}
      </td>
    </tr>
  </table>
</div>
</template>

<style scoped>
.sudoku-table {
  table {
    border-spacing: 0;
    border-width: 0 1px 1px 0;
    border-style: solid;
    border-color: #000;
    margin: 5px;

    td {
      border-width: 1px 0 0 1px;
      border-style: solid;
      border-color: #000;
      padding: 5px;

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
    }
  }
}
</style>
