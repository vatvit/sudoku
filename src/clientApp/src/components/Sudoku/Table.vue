<script setup lang="ts">
import { ref } from 'vue'
import { Table } from "./Table.ts";
import {TableStateDTO} from "./DTO.ts";

const props = defineProps<{
  stateDTO: TableStateDTO,
}>()

const table = new Table(props.stateDTO)

const tableCellsRef = ref(table.cells);

function getColor(col: number, row: number): boolean {
  return !!((Math.floor(col / 3) + Math.floor(row / 3)) % 2)
}

</script>

<template>
<div class="sudoku-table">
  <table>
    <tr v-for="row, colIndex in tableCellsRef">
      <td v-for="cell, rowIndex in row" :class="[getColor(colIndex, rowIndex) ? '' : 'grey' ]">
        {{cell.coords.row}}:{{cell.coords.col}} [{{ cell.groups[0].id }}] {{cell.value}}
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
