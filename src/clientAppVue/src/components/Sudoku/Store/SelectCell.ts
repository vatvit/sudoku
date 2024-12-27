export function setSelectedCell(selectedCellCoords: string) {
  if (selectedCellCoords === '' || this.selectedCell?.coords === selectedCellCoords) {
    resetSelectedCell()
  } else {
    this.selectedCell = this.puzzle.getCellByCoords(selectedCellCoords)
  }
}

export function resetSelectedCell() {
  this.selectedCell = undefined
}
