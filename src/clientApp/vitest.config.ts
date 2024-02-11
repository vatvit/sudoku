/// <reference types="vitest" />

import { defineConfig } from 'vite'
import Vue from '@vitejs/plugin-vue'
import EventSource from 'eventsourcemock';
import vue from 'vue'

const window = {}

window.sudoku = {
    config: {
        mercurePublicUrl: 'testMercurePublicUrl'
    }
}

Object.defineProperty(window, 'EventSource', {
    value: EventSource,
});


export default defineConfig({
    plugins: [
        Vue(),
    ],
    test: {
        globals: true,
        environment: 'jsdom',
    },
})