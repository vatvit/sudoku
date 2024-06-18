import {CellGroupTypes} from "./CellGroup.ts";
import {CellCoords} from "./Cell.ts";

export interface TableStateDto {
    cells: CellDto[][]
}

export interface CellDto {
    row: number
    col: number
    value: number | undefined
    groups: CellGroupDto[]
    protected: boolean
    notes: number[]
}

export interface CellGroupDto {
    id: number
    type: CellGroupTypes
}

export interface MistakeDto {
    cellCoords: CellCoords
}
