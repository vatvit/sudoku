import {Cell} from "./Cell.ts";
import {CellGroupDto} from "./Dto.ts";

export class CellGroup {
    id: number
    type: CellGroupTypes
    cells: Cell[]

    constructor(cellGroupDto: CellGroupDto, cells?: Cell[]) {
        this.id = cellGroupDto.id;
        this.type = cellGroupDto.type;
        this.cells = cells ? cells : [];
    }

}

export enum CellGroupTypes {
    COL = "COL",
    ROW = "ROW",
    SQR = "SQR",
}
