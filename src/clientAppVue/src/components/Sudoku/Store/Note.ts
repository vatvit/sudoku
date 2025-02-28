export function toggleNoteMode() {
  this.isNoteModeEnabled = !this.isNoteModeEnabled
}

export function toggleNoteOnSelectedCell(value) {
  this.selectedCell.hasNote(value) ? this.selectedCell.deleteNote(value) : this.selectedCell.addNote(value)
}
