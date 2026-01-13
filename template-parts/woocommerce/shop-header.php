<?php defined('ABSPATH') || exit; ?>

<div
        class="archive-header"
        x-data="shopHeader()"
>

    <!-- LEFT -->
    <div class="archive-header__left">

        <!-- Toggle Filters (desktop) -->
        <button
                class="archive-header__filters"
                @click="handleFiltersClick"
        >
            <span x-text="filtersLabel"></span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 21V14" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 10V3" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 21V12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 8V3" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 21V16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 12V3" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 14H7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 8H15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17 16H23" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <!-- View toggle -->
        <div class="shop-view-toggle flex items-center gap-2 archive_head_item">
            <button
                    :class="{ active: view === 'grid' }"
                    @click="setView('grid')"
                    aria-label="Προβολή σε Grid"
            >
                <svg width="20px" height="20px" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M7 1H1V7H7V1ZM7 9H1V15H7V9ZM9 1H15V7H9V1ZM15 9H9V15H15V9Z" fill="#000000"/>
                </svg>
            </button>

            <button
                    :class="{ active: view === 'list' }"
                    @click="setView('list')"
                    aria-label="Προβολή σε Λίστα"
            >
                <svg width="20px" height="20px" viewBox="0 0 24 24" id="Layer_1" data-name="Layer 1"
                     xmlns="http://www.w3.org/2000/svg">
                    <line id="primary-upstroke" x1="3.45" y1="6" x2="3.55" y2="6"
                          style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:3px"></line>
                    <line id="primary-upstroke-2" data-name="primary-upstroke" x1="3.45" y1="12" x2="3.55" y2="12"
                          style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:3px"></line>
                    <line id="primary-upstroke-3" data-name="primary-upstroke" x1="3.45" y1="18" x2="3.55" y2="18"
                          style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:3px"></line>
                    <path id="primary" d="M9,6H21M9,12H21M9,18H21"
                          style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"></path>
                </svg>
            </button>
            <div class="text_label" x-text="viewLabel"></div>
        </div>

    </div>

    <!-- CENTER -->
    <div class="archive-header__center archive_head_item">
    <span id="rv-result-count">
        <?php woocommerce_result_count(); ?>
    </span>
    </div>

    <!-- RIGHT -->
    <div class="archive-header__right archive_head_item">

        <div
                class="shop-sorting"
                x-data="shopSorting()"
                @click.outside="open = false"
        >
            <button
                    class="shop-sorting__trigger"
                    @click="open = !open"
                    type="button"
            >
                <span x-text="currentLabel"></span>
                <svg width="14" height="14" viewBox="0 0 20 20">
                    <path d="M6 8l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1"/>
                </svg>
            </button>

            <div
                    class="shop-sorting__dropdown"
                    x-show="open"
                    x-transition
            >
                <template x-for="option in options" :key="option.value">
                    <button
                            type="button"
                            @click="select(option)"
                            x-text="option.label"
                    ></button>
                </template>
            </div>

            <div class="hidden">
                <?php woocommerce_catalog_ordering(); ?>
            </div>
        </div>


    </div>

</div>
