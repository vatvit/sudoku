import {createApp} from 'vue'
import {createPinia} from 'pinia'
import './style.css'
import App from './App.vue'
import axios from "axios";

declare global {
    interface  Window {
        sudoku?: object
    }
}

const response = await axios.get('/api/config');
const config = response.data;

window.sudoku = {
  config: {},
};
window.sudoku.config = config;

const app = createApp(App)

app.use(createPinia())

app.provide('$sudoku', window.sudoku)
app.mount('#app')
