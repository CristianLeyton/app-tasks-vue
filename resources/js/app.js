import './bootstrap';
import { createApp } from "vue";
import router from "./router/index.js";
import NavBar from './components/NavBar.vue'


const app = createApp({});
app.use(router);
app.component('nav-bar', NavBar)
app.mount("#app");