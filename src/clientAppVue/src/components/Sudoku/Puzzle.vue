<script setup lang="ts">
import {ref, watch} from 'vue'
import type {CellGroupDto, PuzzleStateDto} from "./Dto.ts";
import {Puzzle} from "./Puzzle.ts";
import storeFactory from "./Store"
import PuzzleCell from "@/components/Sudoku/Cell.vue";

const props = withDefaults(defineProps<{
  stateDto: PuzzleStateDto,
}>(), {
  stateDto: () => ({id: '', cells: [], groups: [] as CellGroupDto[]})
});

const puzzle = ref(new Puzzle(props.stateDto))

const store = storeFactory(puzzle)

watch(() => props.stateDto, (newVal) => {
  puzzle.value = new Puzzle(newVal)
  store.resetSelectedCell()
  store.resetHighlightValue()
  store.findMistakes()
});

function handleKeyupHandler(event: KeyboardEvent) {
  const value = +event.key
  if (store.selectedCell && !store.selectedCell?.protected) {
    if (store.isNoteModeEnabled) {
      store.toggleNoteOnSelectedCell(value)
    } else {
      if (store.selectedCell.value === value || value === 0) {
        store.selectedCell.deleteValue()
      } else {
        store.selectedCell.value = value
        store.puzzle.setCellValue(store.selectedCell.coords, value)
        store.puzzle.cleanRelatedNotesByCoordsAndValue(store.selectedCell.coords, value)
        store.puzzle.validateSolution()
      }
      store.findMistakes()
    }
  }
  if (!store.selectedCell || store.selectedCell.protected) {
    store.highlightValue(value)
  }
}

</script>

<template>
  <div class="sudoku-puzzle"
       @keyup="handleKeyupHandler"
  >
    <div v-if="puzzle.isSolved">
      SOLVED!
    </div>
    Note mode is <button @click="store.toggleNoteMode">{{ store.isNoteModeEnabled ? 'enabled' : 'disabled' }}</button>
    <table>
      <tr v-for="row in puzzle.cells">
        <td v-for="cell in row" tabindex="0">
          <PuzzleCell
            :cell=cell
            :store=store
          ></PuzzleCell>
        </td>
      </tr>
    </table>
  </div>
</template>

<style scoped>
.sudoku-puzzle {
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
    }
  }

}
</style>
