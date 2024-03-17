import {Cell} from "./Cell.ts";
import {CellGroupDTO} from "./DTO.ts";

export class CellGroup {
    id: number
    type: CellGroupTypes
    cells: Cell[]

    constructor(cellGroupDTO: CellGroupDTO, cells?: Cell[]) {
        this.id = cellGroupDTO.id;
        this.type = cellGroupDTO.type;
        this.cells = cells ? cells : [];
    }

}

export enum CellGroupTypes {
    COL = "COL",
    ROW = "ROW",
    SQR = "SQR",
}
