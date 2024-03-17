import {CellGroupTypes} from "./CellGroup.ts";

export interface TableStateDTO {
    cells: CellDTO[][]
}

export interface CellDTO {
    col: number
    row: number
    value: number | undefined
    groups: CellGroupDTO[]
}

export interface CellGroupDTO {
    id: number
    type: CellGroupTypes
}
