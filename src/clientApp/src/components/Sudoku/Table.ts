import {CellGroup} from "./CellGroup.ts";
import {Cell, CellFactory} from "./Cell.ts";
import {CellDTO, TableStateDTO} from "./DTO.ts";

export interface TableInterface {
    groups: CellGroup[]
    cells: Cell[][]

    setState(tableStateDTO: TableStateDTO): void
}

export class Table implements TableInterface {
    groups: CellGroup[]
    cells: Cell[][]

    constructor(state?: TableStateDTO | undefined) {
        this.groups = []
        this.cells = []

        if (state) {
            this.setState(state)
        }
    }

    setState(tableStateDTO: TableStateDTO): void {
        const allCellGroups: CellGroup[] = []

        tableStateDTO.cells.forEach((col, colIndex) => {
            if (typeof this.cells[colIndex] === "undefined") {
                this.cells[colIndex] = []
            }

            col.forEach((cellDTO: CellDTO, rowIndex) => {
                const cell = CellFactory(cellDTO, allCellGroups)
                this.cells[colIndex][rowIndex] = cell
            })
        })

        this.groups = allCellGroups
    }


}
