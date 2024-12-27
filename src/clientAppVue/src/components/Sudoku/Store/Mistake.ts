import type {MistakeDto} from "@/components/Sudoku/Dto.ts";

export function findMistakes() {
  const mistakes: Map<string, MistakeDto> = new Map<string, MistakeDto>

  this.puzzle.groups.forEach(group => {
    const seenValues = new Map<number, string>();
    group.cells.forEach((cell, coords) => {
      const cellValue = cell.value || 0;
      if (!cellValue) {
        return
      }
      if (seenValues.has(cellValue)) {
        mistakes.set(coords, {cellCoords: coords})
        mistakes.set(seenValues.get(cellValue) as string, {cellCoords: seenValues.get(cellValue) as string})
      } else {
        seenValues.set(cellValue, coords)
      }
    });
  });

  this.mistakes = new Map<string, MistakeDto>(mistakes)
}
