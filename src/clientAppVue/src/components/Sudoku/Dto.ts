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
