import {CellGroupTypes} from "./CellGroup.ts";

export interface PuzzleStateDto {
  id: string
  puzzle: CellDto[][]
  groups: CellGroupDto[]
  cellValues: Record<string, number>
  notes: Record<string, number[]>
}

export interface CellDto {
  coords: string
  value: number
  notes: number[]
}

export interface Coords {
  row: number
  col: number
}

export interface CellGroupDto {
  id: number
  type: CellGroupTypes
  cells: Set<string>
}

export interface MistakeDto {
  cellCoords: string
}

export interface ActionDto {
  id: string // idempotency ID
  timeDiff: number // milliseconds from start
  effects: ActionEffectDto[]
}

export interface ActionEffectDto {
  coords: string
  value: number | undefined
  notes: number[]
}
