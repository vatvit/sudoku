import {CellGroup} from "./CellGroup.ts";
import {Cell, CellCoords, CellFactory} from "./Cell.ts";
import {CellDto, MistakeDto, TableStateDto, TableValidationResultDto} from "./Dto.ts";

export class Table {
    private _groups: CellGroup[]
    private _cells: Cell[][]
    private _solved: boolean
    private _mistakes: Map<CellCoords, MistakeDto>

    constructor(state?: TableStateDto | undefined) {
        this._groups = []
        this._cells = []
        this._solved = false

        if (state) {
            this.setState(state)
            this.validateSolution()
        }
    }

    get groups(): CellGroup[] {
        return this._groups;
    }

    get cells(): Cell[][] {
        return this._cells;
    }

    get isSolved(): boolean {
        return this._solved
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

    cleanNotesByCellValue(cell: Cell) {
        this._groups.forEach(group => {
            if (group.cells.includes(cell)) {
                group.cells.forEach(groupCell => {
                    groupCell.deleteNote(cell.value);
                });
            }
        });
    }

    validateSolution(): boolean {
        this._mistakes = new Map<CellCoords, MistakeDto>()

        for (const group of this._groups) {
            const cellValues = group.cells.map(cell => cell.value)

            if (
                cellValues.includes(0) ||
                new Set(cellValues).size !== cellValues.length ||
                cellValues.some(val => val < 1 || val > group.cells.length)
            ) {
                this._solved = false

                return this._solved
            }
        }

        this._solved = true

        return this._solved
    }

}
