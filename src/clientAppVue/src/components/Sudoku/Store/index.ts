import {defineStore} from "pinia";
import type {Puzzle} from "@/components/Sudoku/Puzzle.ts";
import type {Cell} from "@/components/Sudoku/Cell.ts";
import {CellGroup} from "@/components/Sudoku/CellGroup.ts";
import type {MistakeDto} from "@/components/Sudoku/Dto.ts";
import {resetSelectedCell, setSelectedCell} from "./SelectCell.ts"
import {hoverCell, leaveCell} from "./HoverCell.ts";
import {toggleNoteMode, toggleNoteOnSelectedCell} from "./Note.ts";
import {findMistakes} from "./Mistake.ts";
import {highlightValue, resetHighlightValue} from "./HighlightValue.ts";

export interface SudokuStore extends ReturnType<typeof useSudokuStore> {}

export default (puzzle: Puzzle) => {
  const useSudokuStore = defineStore('sudoku', {
    state() {
      return {
        _puzzle: puzzle,
        selectedCell: undefined as Cell | undefined,
        highlightedValue: 0,
        hoveredCell: undefined as Cell | undefined,
        hoveredCellGroups: [] as CellGroup[],
        mistakes: new Map<string, MistakeDto>,
        isNoteModeEnabled: false,
      }
    },
    getters: {
      puzzle(): Puzzle {
        return this._puzzle
      }
    },
    actions: {
      setSelectedCell,
      resetSelectedCell,
      highlightValue,
      resetHighlightValue,
      hoverCell,
      leaveCell,
      findMistakes,
      toggleNoteMode,
      toggleNoteOnSelectedCell,
    },
  })
  return useSudokuStore()
}
