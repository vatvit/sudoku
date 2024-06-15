import {CellGroup} from "./CellGroup.ts";
import {CellDto, CellGroupDto} from "./Dto.ts";

export class Cell {
    private readonly _coords: CellCoords
    private _value: number|undefined
    private readonly _groups: CellGroup[]
    private readonly _protected: boolean

    constructor(
        cellDto: CellDto,
        groups: CellGroup[],
    ) {
        this._coords = new CellCoords(cellDto.row, cellDto.col)
        this._value = cellDto.value
        this._groups = groups
        this._protected = cellDto.protected

        this.fulfillGroups()
    }

    get coords(): CellCoords {
        return this._coords;
    }

    get groups(): CellGroup[] {
        return this._groups;
    }

    get value(): number | undefined {
        return this._value;
    }

    set value(value) {
        if (!this._protected) {
            this._value = value;
        }
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

    const cell = new Cell(cellDto, cellGroups)

    return cell
}

export class CellCoords {
    row: number
    col: number

    constructor(row: number, col: number) {
        this.row = row
        this.col = col
    }
}
