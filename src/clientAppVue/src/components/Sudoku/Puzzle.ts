import {CellGroup} from "./CellGroup.ts";
import {Cell} from "./Cell.ts";
import type {ActionDto, ActionEffectDto, CellDto, CellGroupDto, PuzzleStateDto} from "./Dto.ts";
import {Api} from "@/generated/Api";

const api = new Api().api;

export class Puzzle {
  public readonly id: string
  private _initialised: boolean = false
  private readonly _groups: CellGroup[] = []
  private readonly _cells: Cell[][]
  private _solved: boolean

  constructor(state: PuzzleStateDto) {
    this.id = state.id
    this._groups = []
    this._cells = []
    this._solved = false

    if (state?.cells.length > 0) {
      this.initialise(state)
    }
  }

  initialise(state: PuzzleStateDto): void {
    if (this._initialised) {
      throw new Error('Puzzle already initialised')
    }
    this.setState(state)
    this.validateSolution()
    this._initialised = true
  }

  get initialised(): boolean {
    return this._initialised
  }

  get groups(): CellGroup[] {
    return this._groups || [];
  }

  get cells(): Cell[][] {
    return this._cells;
  }

  getRowColByCoords(coords: string): number[] {
    const coordsArray: string[] = coords.split(":");
    const row: number = parseInt(coordsArray[0]);
    const col: number = parseInt(coordsArray[1]);
    return [row, col]
  }

  getRowColIndexesByCoords(coords: string): number[] {
    const [row, col] = this.getRowColByCoords(coords)
    return [row - 1, col - 1]
  }

  getCellByCoords(coords: string): Cell {
    const [rowIndex, colIndex] = this.getRowColIndexesByCoords(coords)
    return this._cells[rowIndex][colIndex]
  }

  getCellGroupsByCoords(coords: string): CellGroup[] {
    return this._groups.filter((cellGroup: CellGroup) => cellGroup.cells.has(coords))
  }

  get isSolved(): boolean {
    return this._solved
  }

  private setState(puzzleStateDto: PuzzleStateDto): void {
    puzzleStateDto.groups.forEach((cellGroupDto: CellGroupDto) => {
      const groupCells: Map<string, Cell> = new Map<string, Cell>()
      cellGroupDto.cells.forEach((coords: string) => {
        const [rowIndex, colIndex] = this.getRowColIndexesByCoords(coords)
        const cellDto = {
          coords: coords,
          value: puzzleStateDto.cells[rowIndex][colIndex].value,
          protected: puzzleStateDto.cells[rowIndex][colIndex].value > 0 || false,
          notes: [],
        } as CellDto // TODO: fix it.

        if (typeof this._cells[rowIndex] === 'undefined') {
          this._cells[rowIndex] = []
        }
        if (typeof this._cells[rowIndex][colIndex] === 'undefined') {
          this._cells[rowIndex][colIndex] = new Cell(cellDto);
        }
        groupCells.set(cellDto.coords, this._cells[rowIndex][colIndex])
      })

      this._groups.push(new CellGroup(cellGroupDto, groupCells))
    })
  }

  async setCellValue(coords: string, value: number): void {
    const cell: Cell = this.getCellByCoords(coords)
    cell.value = value

    const actionDto: ActionDto = {
      id: '1234',
      timeDiff: 1234,
      effects: <ActionEffectDto[]>[
        {
          coords: cell.coords,
          value: cell.value
        }
      ]
    }

    const response = await api.postCreateGameSudokuInstanceAction(this.id, actionDto)
  }

  cleanRelatedNotesByCoordsAndValue(coords: string, value: number): void {
    this._groups.forEach(group => {
      if (group.cells.has(coords)) {
        group.cells.forEach(groupCell => {
          groupCell.deleteNote(value);
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
