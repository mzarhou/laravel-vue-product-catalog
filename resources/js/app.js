import './bootstrap';
import { createApp } from 'vue';
import ProductList from './components/product-list.vue';

const app = createApp({});

app.component('product-list', ProductList)

app.mount('#app');
