export function hoverCell(coords: string) {
  if (!coords) {
    return
  }
  this.hoveredCell = this.puzzle.getCellByCoords(coords)
  this.hoveredCellGroups = this.puzzle.getCellGroupsByCoords(coords)
}

export function leaveCell(coords: string) {
  if (!this.hoveredCell) {
    return
  }
  if (coords === this.hoveredCell.coords) {
    this.hoveredCell = undefined
    this.hoveredCellGroups = []
  }
}
