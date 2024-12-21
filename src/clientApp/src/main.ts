import {createApp} from 'vue'
import {createPinia} from 'pinia'
import './style.css'
import App from './App.vue'

declare global {
    interface  Window {
        sudoku?: object
    }
}

const pinia = createPinia()
const app = createApp(App)
app.use(pinia)
app.provide('$sudoku', window.sudoku || {})
app.mount('#app')
