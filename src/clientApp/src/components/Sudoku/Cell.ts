import {CellGroup} from "./CellGroup.ts";
import {CellDTO, CellGroupDTO} from "./DTO.ts";

export class Cell {
    coords: CellCoords
    groups: CellGroup[]
    value: number|undefined

    constructor(col: number, row: number, value: number|undefined, groups?: CellGroup[]) {
        this.coords = new CellCoords(col, row)
        this.value = value
        this.groups = groups ? groups : []
    }
}

export function CellFactory(cellDTO: CellDTO, allCellGroups: CellGroup[]) {
    const cell = new Cell(cellDTO.col, cellDTO.row, cellDTO.value)

    const cellGroups: CellGroup[] = []
    cellDTO.groups.forEach((groupDTO: CellGroupDTO) => {
        let findCellGroup = allCellGroups.find((cellGroup: CellGroup) => cellGroup.id === groupDTO.id)
        if (!findCellGroup) {
            findCellGroup = new CellGroup(groupDTO)
            allCellGroups.push(findCellGroup)
        }
        cellGroups.push(findCellGroup)
        findCellGroup.cells.push(cell)
    })

    cell.groups = cellGroups

    return cell
}

export class CellCoords {
    col: number
    row: number

    constructor(col: number, row: number) {
        this.col = col
        this.row = row
    }
}
