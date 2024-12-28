import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/components/HomePage.vue'
import SudokuPuzzlePage from '@/components/Sudoku/PuzzlePage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/puzzle/:puzzleId',
    name: 'SudokuPuzzle',
    component: SudokuPuzzlePage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
