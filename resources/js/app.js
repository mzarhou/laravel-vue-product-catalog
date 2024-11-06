import './bootstrap';
import { createApp } from 'vue';
import ProductList from './components/product-list.vue';
import ProductForm from './components/product-form.vue';

const app = createApp({});

app.component('product-list', ProductList)
app.component('product-form', ProductForm)

app.mount('#app');
