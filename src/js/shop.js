import { initFilters, initProductCatExclusiveFilter } from './modules/woocommerce/filters.js';
import { initLoadMoreProducts } from './modules/woocommerce/load-more.js';
import { initProductList }      from './modules/woocommerce/product-list.js';
import { initProductCatalogs }  from './modules/woocommerce/single-product/product-catalogs.js';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initProductCatExclusiveFilter();
    initLoadMoreProducts();
    initProductList();
    initProductCatalogs();
});
