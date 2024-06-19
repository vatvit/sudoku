import {Cell} from "./Cell.ts";
import {CellGroupDto} from "./Dto.ts";

export class CellGroup {
    id: number
    type: CellGroupTypes
    cells: Map<string, Cell>

    constructor(cellGroupDto: CellGroupDto, cells?: Map<string, Cell>) {
        this.id = cellGroupDto.id;
        this.type = cellGroupDto.type;
        this.cells = cells ? cells : new Map<string, Cell>();
    }

}

export enum CellGroupTypes {
    COL = "COL",
    ROW = "ROW",
    SQR = "SQR",
}
