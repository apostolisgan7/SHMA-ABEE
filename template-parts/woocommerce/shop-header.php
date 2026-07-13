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


    </div>


    <!-- RIGHT -->
    <div class="archive-header__right">
        <!-- View toggle -->
        <?php
        $grid_active_svg   = get_template_directory() . '/src/img/icons/GRID-ACTIVE.svg';
        $grid_inactive_svg = get_template_directory() . '/src/img/icons/GRID-INACTIVE.svg';
        $list_active_svg   = get_template_directory() . '/src/img/icons/LIST-ACTIVE.svg';
        $list_inactive_svg = get_template_directory() . '/src/img/icons/LIST-INACTIVE.svg';
        ?>
        <div class="shop-view-toggle flex items-center gap-2 archive_head_item">
            <button
                    :class="{ active: view === 'grid' }"
                    @click="setView('grid')"
                    aria-label="Προβολή σε Grid"
            >
                <span x-show="view === 'grid'" x-cloak><?php if (file_exists($grid_active_svg)) echo file_get_contents($grid_active_svg); ?></span>
                <span x-show="view !== 'grid'" x-cloak><?php if (file_exists($grid_inactive_svg)) echo file_get_contents($grid_inactive_svg); ?></span>
            </button>

            <button
                    :class="{ active: view === 'list' }"
                    @click="setView('list')"
                    aria-label="Προβολή σε Λίστα"
            >
                <span x-show="view === 'list'" x-cloak><?php if (file_exists($list_active_svg)) echo file_get_contents($list_active_svg); ?></span>
                <span x-show="view !== 'list'" x-cloak><?php if (file_exists($list_inactive_svg)) echo file_get_contents($list_inactive_svg); ?></span>
            </button>
            <div class="text_label" x-text="viewLabel"></div>
        </div>
        <span id="rv-result-count">
        <?php woocommerce_result_count(); ?>
    </span>

        <div
                class="shop-sorting archive_head_item"
                x-data="shopSorting()"
                @click.outside="open = false"
        >
            <button
                    class="shop-sorting__trigger"
                    :class="{ 'is-open': open }"
                    @click="open = !open"
                    type="button"
            >
                <span x-text="currentLabel"></span>
                <span class="shop-sorting__icon">
                    <svg width="14" height="14" viewBox="0 0 20 20">
                        <path d="M6 8l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1"/>
                    </svg>
                </span>
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
