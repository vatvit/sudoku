import {CellGroup} from "./CellGroup.ts";
import {Cell, CellFactory} from "./Cell.ts";
import {CellDTO, TableStateDTO} from "./DTO.ts";

export class Table {
    private _groups: CellGroup[]
    private _cells: Cell[][]

    constructor(state?: TableStateDTO | undefined) {
        this._groups = []
        this._cells = []

        if (state) {
            this.setState(state)
        }
    }

    get groups(): CellGroup[] {
        return this._groups;
    }

    get cells(): Cell[][] {
        return this._cells;
    }

    setState(tableStateDTO: TableStateDTO): void {
        const allCellGroups: CellGroup[] = []

        tableStateDTO.cells.forEach((row, rowIndex) => {
            if (typeof this._cells[rowIndex] === "undefined") {
                this._cells[rowIndex] = []
            }

            row.forEach((cellDTO: CellDTO, colIndex) => {
                const cell = CellFactory(cellDTO, allCellGroups)
                this._cells[rowIndex][colIndex] = cell
            })
        })

        this._groups = allCellGroups
    }


}
