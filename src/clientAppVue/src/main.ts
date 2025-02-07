import {createApp} from 'vue'
import {createPinia} from 'pinia'
import './style.css'
import App from './App.vue'
import router from "@/router";
import {Api} from "@/generated/Api";

const api = new Api().api;

declare global {
    interface  Window {
        sudoku?: object
    }
}

const response = await api.getGetConfig();
const config = response.data;

window.sudoku = {
  config: {},
};
window.sudoku.config = config;

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.provide('$sudoku', window.sudoku)
app.mount('#app')
