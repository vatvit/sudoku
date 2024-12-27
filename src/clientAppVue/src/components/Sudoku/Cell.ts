import type {CellDto} from "./Dto.ts";

export class Cell {
  private readonly _coords: string
  private _value: number
  private readonly _protected: boolean
  private _notes: Set<number>

  constructor(
    cellDto: CellDto,
  ) {
    this._coords = cellDto.coords
    this._value = +(cellDto.value as number) || 0
    this._protected = cellDto.protected
    this._notes = new Set([])

    this.setNotes(cellDto.notes || [])
  }

  get coords(): string {
    return this._coords;
  }

  get value(): number {
    return this._value;
  }

  set value(value: number) {
    if (!this._protected && Cell.validateValue(value)) {
      this._value = value;
    }
  }

  deleteValue() {
    this._value = 0
  }

  getNotes(): number[] {
    return [...this._notes]
  }

  setNotes(notes: number[]) {
    this.clearNotes()
    notes.forEach(this.addNote)
  }

  clearNotes() {
    this._notes = new Set([])
  }

  addNote(note: number) {
    if (Cell.validateValue(note)) {
      this._notes.add(note)
    }
  }

  hasNote(note: number): boolean {
    return this._notes.has(note)
  }

  deleteNote(note: number): void {
    this._notes.delete(note)
  }

  get protected(): boolean {
    return this._protected
  }

  static validateValue(value: number): boolean {
    return value > 0 && value <= 9
  }
}
