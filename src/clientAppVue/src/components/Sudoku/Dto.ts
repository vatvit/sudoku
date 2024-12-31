import {CellGroupTypes} from "./CellGroup.ts";

export interface PuzzleStateDto {
  id: string
  cells: CellDto[][]
  groups: CellGroupDto[]
}

export interface CellDto {
  coords: string
  value: number | undefined
  protected: boolean
  notes: number[]
}

export interface CellGroupDto {
  id: number
  type: CellGroupTypes
  cells: Map<string, CellDto>
}

export interface MistakeDto {
  cellCoords: string
}
