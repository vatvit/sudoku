import {CellGroup} from "./CellGroup.ts";
import {CellDTO, CellGroupDTO} from "./DTO.ts";

export class Cell {
    private _coords: CellCoords
    private _groups: CellGroup[]
    private _value: number|undefined

    constructor(row: number, col: number, value: number|undefined, groups: CellGroup[]) {
        this._coords = new CellCoords(row, col)
        this._value = value
        this._groups = groups

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

export function CellFactory(cellDTO: CellDTO, allCellGroups: CellGroup[]) {
    const cellGroups: CellGroup[] = []
    cellDTO.groups.forEach((groupDTO: CellGroupDTO) => {
        let findCellGroup = allCellGroups.find((cellGroup: CellGroup) => {
            return cellGroup.id === groupDTO.id
                && cellGroup.type === groupDTO.type
        })
        if (!findCellGroup) {
            findCellGroup = new CellGroup(groupDTO)
            allCellGroups.push(findCellGroup)
        }
        cellGroups.push(findCellGroup)
    })

    const cell = new Cell(cellDTO.row, cellDTO.col, cellDTO.value, cellGroups)

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
