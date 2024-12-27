export function toggleNoteMode() {
  this.isNoteModeEnabled = !this.isNoteModeEnabled
}

export function toggleNoteOnSelectedCell(value) {
  this.selectedCell.hasNote(key) ? this.selectedCell.deleteNote(key) : this.selectedCell.addNote(key)
}
