import { initFilters }          from './modules/woocommerce/filters.js';
import { initLoadMoreProducts } from './modules/woocommerce/load-more.js';
import { initProductList }      from './modules/woocommerce/product-list.js';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initLoadMoreProducts();
    initProductList();
});
