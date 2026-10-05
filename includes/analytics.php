<?php
/**
 * Google Tag Manager
 *
 * Loads only when the environment type is "production" (the WordPress default
 * when WP_ENVIRONMENT_TYPE is not defined), so local/staging visits don't
 * pollute analytics data.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RUINED_GTM_ID', 'GTM-NRGZ5B7V');

function ruined_gtm_enabled() {
    return RUINED_GTM_ID && wp_get_environment_type() === 'production';
}

// Container snippet — as high in <head> as possible.
add_action('wp_head', function () {
    if (!ruined_gtm_enabled()) {
        return;
    }
    ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js(RUINED_GTM_ID); ?>');</script>
<!-- End Google Tag Manager -->
    <?php
}, 1);

// noscript fallback — immediately after <body>.
add_action('wp_body_open', function () {
    if (!ruined_gtm_enabled()) {
        return;
    }
    ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr(RUINED_GTM_ID); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <?php
}, 1);
