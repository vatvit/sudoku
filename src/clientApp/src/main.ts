import {createApp} from 'vue'
import './style.css'
import App from './App.vue'

declare global {
    interface  Window {
        sudoku?: object
    }
}

const app = createApp(App)
app.provide('$sudoku', window.sudoku || {})
app.mount('#app')
