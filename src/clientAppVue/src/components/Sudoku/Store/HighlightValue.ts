import {Cell} from "@/components/Sudoku/Cell.ts";

export function highlightValue(value: number) {
  if (Cell.validateValue(value)) {
    this.highlightedValue = value
  } else {
    this.highlightedValue = this.resetHighlightValue()
  }
}

export function resetHighlightValue() {
  this.highlightedValue = 0
}

