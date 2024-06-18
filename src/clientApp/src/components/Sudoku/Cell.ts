import {CellGroup} from "./CellGroup.ts";
import {CellDto, CellGroupDto, MistakeDto} from "./Dto.ts";

export class Cell {
    private readonly _coords: CellCoords
    private _value: number
    private readonly _groups: CellGroup[]
    private readonly _protected: boolean
    private _notes: Set<number>

    constructor(
        cellDto: CellDto,
        groups: CellGroup[],
    ) {
        this._coords = new CellCoords(cellDto.row, cellDto.col)
        this._value = +cellDto.value || 0
        this._groups = groups
        this._protected = cellDto.protected
        this._notes = new Set([])

        this.setNotes(cellDto.notes || [])
        this.fulfillGroups()
    }

    get coords(): CellCoords {
        return this._coords;
    }

    get groups(): CellGroup[] {
        return this._groups;
    }

    get value(): number {
        return this._value;
    }

    set value(value: number) {
        if (!this._protected && this.validateValue(value)) {
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
        if (this.validateValue(note)) {
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

    fulfillGroups() {
        this._groups.forEach((cellGroup) => {
            if (!cellGroup.cells.find((cell) => {
                return cell === this
            })) {
                cellGroup.cells.push(this)
            }
        })
    }

    validateValue(value: number): boolean {
        return value > 0 && value <= 9
    }
}

export function CellFactory(cellDto: CellDto, allCellGroups: CellGroup[]) {
    const cellGroups: CellGroup[] = []
    cellDto.groups.forEach((groupDto: CellGroupDto) => {
        let findCellGroup = allCellGroups.find((cellGroup: CellGroup) => {
            return cellGroup.id === groupDto.id
                && cellGroup.type === groupDto.type
        })
        if (!findCellGroup) {
            findCellGroup = new CellGroup(groupDto)
            allCellGroups.push(findCellGroup)
        }
        cellGroups.push(findCellGroup)
    })

    return new Cell(cellDto, cellGroups)
}

export class CellCoords {
    readonly row: number
    readonly col: number

    constructor(row: number, col: number) {
        this.row = row
        this.col = col
    }
}
