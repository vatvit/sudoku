import {CellGroup} from "./CellGroup.ts";
import {Cell} from "./Cell.ts";
import type {CellDto, CellGroupDto, TableStateDto} from "./Dto.ts";

export class Table {
    private _groups: CellGroup[] = []
    private _cells: Cell[][]
    private _solved: boolean

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
        return this._groups || [];
    }

    get cells(): Cell[][] {
        return this._cells;
    }

    get isSolved(): boolean {
        return this._solved
    }

    setState(tableStateDto: TableStateDto): void {
        tableStateDto.groups.forEach((cellGroupDto: CellGroupDto) => {
            const groupCells: Map<string, Cell> = new Map<string, Cell>()
            cellGroupDto.cells.forEach((cellDto: CellDto, coords: string) => {
                const coordsArray: string[] = coords.split(":");
                const row: number = parseInt(coordsArray[0]);
                const col: number = parseInt(coordsArray[1]);

                if (typeof this._cells[row - 1] === 'undefined') {
                    this._cells[row - 1] = []
                }
                if (typeof this._cells[row - 1][col - 1] === 'undefined') {
                    this._cells[row - 1][col - 1] = new Cell(cellDto);
                }
                groupCells.set(row + ':' + col, this._cells[row - 1][col - 1])
            })

            this._groups.push(new CellGroup(cellGroupDto, groupCells))
        })
    }

    cleanNotesByCellValue(cell: Cell) {
        this._groups.forEach(group => {
            if (group.cells.has(cell.coords)) {
                group.cells.forEach(groupCell => {
                    groupCell.deleteNote(cell.value);
                });
            }
        });
    }

    validateSolution(): boolean {
        for (const group of this._groups) {
            const cellValues: number[] = Array.from(group.cells.values()).map((cell: Cell) => cell.value)

            if (
                cellValues.includes(0) ||
                new Set(cellValues).size !== cellValues.length ||
                cellValues.some(val => val < 1 || val > 9)
            ) {
                this._solved = false

                return this._solved
            }
        }

        this._solved = true

        return this._solved
    }

}
