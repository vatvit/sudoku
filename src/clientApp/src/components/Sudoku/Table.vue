<script setup lang="ts">
import { ref, watch } from 'vue'
import { Table } from "./Table.ts";
import {TableStateDTO} from "./DTO.ts";
import {Cell} from "./Cell.ts";

const props = withDefaults(defineProps<{
  stateDTO: TableStateDTO,
}>(), {
  stateDTO: () => ({cells: []})
});

const table = ref(new Table(props.stateDTO))

watch( () => props.stateDTO, (newVal) => {
  table.value = new Table(newVal)
});

function getColor(col: number, row: number): boolean {
  return !!((Math.floor((col - 1) / 3) + Math.floor((row - 1) / 3)) % 2)
}

function getCellClasses(cell: Cell): string[] {
  const classes = []

  const groupColor = getColor(cell.coords.col, cell.coords.row) ? '' : 'grey'
  const colClass = 'col-' + cell.coords.col
  const rowClass = 'row-' + cell.coords.row

  classes.push([groupColor, colClass, rowClass])
  return classes;
}

</script>

<template>
<div class="sudoku-table">
  <table>
    <tr v-for="row in table.cells">
      <td v-for="cell in row"
          :class="getCellClasses(cell)"
      >
        {{cell.value}}
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
    }
  }
}
</style>
