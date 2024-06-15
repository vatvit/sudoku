import {CellGroup} from "./CellGroup.ts";
import {Cell, CellFactory} from "./Cell.ts";
import {CellDto, TableStateDto} from "./Dto.ts";

export class Table {
    private _groups: CellGroup[]
    private _cells: Cell[][]

    constructor(state?: TableStateDto | undefined) {
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

    setState(tableStateDto: TableStateDto): void {
        const allCellGroups: CellGroup[] = []

        tableStateDto.cells.forEach((row, rowIndex) => {
            if (typeof this._cells[rowIndex] === "undefined") {
                this._cells[rowIndex] = []
            }

            row.forEach((cellDto: CellDto, colIndex) => {
                this._cells[rowIndex][colIndex] = CellFactory(cellDto, allCellGroups)
            })
        })

        this._groups = allCellGroups
    }

    validateSolution(): boolean {
        for (const group of this._groups) {
            const cellValues = group.cells.map(cell => cell.value);

            if (
                cellValues.includes(undefined) ||
                new Set(cellValues).size !== cellValues.length ||
                cellValues.some(val => val < 1 || val > group.cells.length)
            ) {
                return false;
            }
        }

        return true;
    }

}
