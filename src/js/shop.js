import { initFilters }          from './modules/woocommerce/filters.js';
import { initLoadMoreProducts } from './modules/woocommerce/load-more.js';
import { initProductList }      from './modules/woocommerce/product-list.js';
import { initProductCatalogs }  from './modules/woocommerce/single-product/product-catalogs.js';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initLoadMoreProducts();
    initProductList();
    initProductCatalogs();
});
