<?php
use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;

if ( ! defined('DGWT_WCAS_FILE') ) exit;

$uniqueID = ++ DGWT_WCAS()->searchInstances;
$layout   = Helpers::getLayoutSettings();
$iconType = $layout->icon;
?>
<div class="rv-searchbar">
    <form class="dgwt-wcas-search-form" role="search" action="<?php echo Helpers::searchFormAction(); ?>" method="get">
        <div class="rv-searchbar__inner">
            <?php echo Helpers::getMagnifierIco('dgwt-wcas-ico-magnifier', $iconType); ?>
            <label class="screen-reader-text" for="dgwt-wcas-search-input-<?php echo $uniqueID; ?>">
                <?php _e('Products search','ajax-search-for-woocommerce'); ?>
            </label>

            <input
                    id="dgwt-wcas-search-input-<?php echo $uniqueID; ?>"
                    type="search"
                    class="dgwt-wcas-search-input"
                    name="<?php echo Helpers::getSearchInputName(); ?>"
                    value="<?php echo apply_filters('dgwt/wcas/search_bar/value', get_search_query(), DGWT_WCAS()->searchInstances ); ?>"
                    placeholder="<?php echo esc_attr( Helpers::getLabel('search_placeholder') ); ?>"
                    autocomplete="off"
            />
            <div class="dgwt-wcas-preloader"></div>

            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="dgwt_wcas" value="1" />
            <?php if ( Multilingual::isWPML() ): ?>
                <input type="hidden" name="lang" value="<?php echo esc_attr( Multilingual::getCurrentLanguage() ); ?>" />
            <?php endif; ?>
        </div>
    </form>
</div>
