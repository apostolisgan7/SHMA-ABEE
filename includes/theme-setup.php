<?php
/**
 * Theme setup and support - Minimal configuration
 */
add_action('after_setup_theme', 'ruined_theme_setup');
add_action('admin_bar_menu', 'ruined_add_template_info_to_admin_bar', 1000);
add_action('init', 'ruined_add_post_thumbnail_support', 0); // Changed priority to 0 to run earlier
add_action('current_screen', 'ruined_ensure_featured_image_support'); // Changed to current_screen hook

function ruined_theme_setup() {
    // Let WordPress manage the document title
    add_theme_support('title-tag');
    
    // Add support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add support for custom logo
    add_theme_support('custom-logo', [
        'height'      => 190,
        'width'       => 190,
        'flex-width'  => true,
        'flex-height' => true,
    ]);
    
    // WooCommerce support (if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
    }
    
    // Register navigation menus
    register_nav_menus([
        'primary'      => __('Primary Menu', 'ruined'),
        'catalog-menu' => __('Catalog Menu', 'ruined'),
        'mobile-menu'  => __('Mobile Menu', 'ruined'),
        'social-menu' => __('Social Menu', 'ruined'),
        'support-menu'  => __('Support / Contact Menu', 'ruined'),
        'bottom-main-menu' => __('Main Menu Bottom', 'ruined'),
    ]);
}

/**
 * Ensure post thumbnail support is enabled for all public post types
 */
function ruined_add_post_thumbnail_support() {
    $post_types = get_post_types(['public' => true], 'names');
    
    foreach ($post_types as $post_type) {
        if (!post_type_supports($post_type, 'thumbnail')) {
            add_post_type_support($post_type, 'thumbnail');
        }
    }
}

/**
 * Ensure featured image support is enabled for posts and pages
 */
function ruined_ensure_featured_image_support() {
    // Only run in admin and on post edit screens
    if (!is_admin()) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->base, ['post', 'edit'])) {
        return;
    }
    
    // Get the post type
    $post_type = $screen->post_type;
    
    // If it's a post or page, ensure thumbnail support is enabled
    if (in_array($post_type, ['post', 'page'])) {
        if (!post_type_supports($post_type, 'thumbnail')) {
            add_post_type_support($post_type, 'thumbnail');
        }
        
        // Force the meta box to be shown
        add_meta_box(
            'postimagediv',
            __('Featured Image'),
            'post_thumbnail_meta_box',
            $post_type,
            'side',
            'low'
        );
    }
}



function ruined_register_page_templates($templates) {
    // Define the directory to scan for template files
    $theme_directory = get_stylesheet_directory();
    $template_files = [];

    // Scan the theme directory for PHP files that might be templates
    $files = glob($theme_directory . '/*.php');

    foreach ($files as $file) {
        // Skip if not a PHP file
        if (!preg_match('/\.php$/', $file)) {
            continue;
        }

        // Skip specific files that shouldn't be templates
        $skip_files = ['functions.php', 'header.php', 'footer.php', 'sidebar.php', 'index.php', 'style.css'];
        if (in_array(basename($file), $skip_files)) {
            continue;
        }

        // Get file content to check for template header
        $file_content = file_get_contents($file);

        // Check if the file has a template name header
        if (preg_match('/Template\s*Name:\s*(.*)$/mi', $file_content, $matches)) {
            $template_name = trim($matches[1]);
            $template_file = basename($file);
            $templates[$template_file] = $template_name;
        }
    }

    return $templates;
}
add_filter('theme_page_templates', 'ruined_register_page_templates');





function rv_should_show_pages_hero() {

    // Αρχική – δεν το θες
    if ( is_front_page() ) {
        return false;
    }

    // Blog home (αν έχεις) – ας το κλείσουμε επίσης
    if ( is_home() && ! is_front_page() ) {
        return false;
    }

    // Single product – δεν το θες
    if ( function_exists('is_product') && is_product() ) {
        return false;
    }

    // Αν θες να εξαιρείς και συγκεκριμένα templates, π.χ. template-no-hero.php:
     if ( is_page_template('template-no-hero.php') ) {
         return false;
     }

    // Όλα τα άλλα ΟΚ
    return true;
}

/**
 * Always show Pages Hero on WooCommerce archives (shop, category, tag, search)
 */
function rv_show_pages_hero() {
    if ( ! rv_should_show_pages_hero() ) {
        return;
    }

    get_template_part( 'template-parts/header/pages-hero' );
}



function rv_show_pages_hero_woo() {

    if ( is_product() ) {
        return;
    }

    if ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) {
        get_template_part( 'template-parts/header/pages-hero' );
    }
}
add_action( 'woocommerce_before_main_content', 'rv_show_pages_hero_woo', 5 );





/**
 * Add current template information to the admin bar
 */
function ruined_add_template_info_to_admin_bar($wp_admin_bar) {
    if (!is_admin() && is_admin_bar_showing()) {
        global $template;
        $template_name = basename($template);
        $template_path = str_replace(ABSPATH, '', $template);
        
        // Get template hierarchy
        $hierarchy = [];
        if (is_404()) {
            $hierarchy[] = '404.php';
        } elseif (is_search()) {
            $hierarchy[] = 'search.php';
        } elseif (is_front_page()) {
            $hierarchy[] = 'front-page.php';
        } elseif (is_home()) {
            $hierarchy[] = 'home.php';
            $hierarchy[] = 'index.php';
        } elseif (is_post_type_archive()) {
            $post_type = get_post_type();
            $hierarchy[] = 'archive-' . $post_type . '.php';
            $hierarchy[] = 'archive.php';
            $hierarchy[] = 'index.php';
        } elseif (is_tax() || is_category() || is_tag()) {
            $term = get_queried_object();
            if ($term) {
                if (!empty($term->taxonomy)) {
                    $hierarchy[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
                    $hierarchy[] = 'taxonomy-' . $term->taxonomy . '.php';
                }
                $hierarchy[] = 'archive.php';
                $hierarchy[] = 'index.php';
            }
        } elseif (is_singular()) {
            $post = get_queried_object();
            if ($post) {
                $hierarchy[] = 'single-' . $post->post_type . '-' . $post->post_name . '.php';
                $hierarchy[] = 'single-' . $post->post_type . '.php';
                $hierarchy[] = 'single.php';
                $hierarchy[] = 'singular.php';
                $hierarchy[] = 'index.php';
            }
        } elseif (is_archive()) {
            $hierarchy[] = 'archive.php';
            $hierarchy[] = 'index.php';
        } else {
            $hierarchy[] = 'index.php';
        }
        
        // Add parent node
        $wp_admin_bar->add_node([
            'id'    => 'template-info',
            'title' => 'Template: ' . $template_name,
            'href'  => '#',
            'meta'  => ['title' => $template_path]
        ]);
        
        // Add template path
        $wp_admin_bar->add_node([
            'id'     => 'template-path',
            'parent' => 'template-info',
            'title'  => 'Path: ' . $template_path
        ]);
        
        // Add template hierarchy
        $wp_admin_bar->add_node([
            'id'     => 'template-hierarchy',
            'parent' => 'template-info',
            'title'  => 'Template Hierarchy:'
        ]);
        
        foreach ($hierarchy as $index => $hierarchy_item) {
            $wp_admin_bar->add_node([
                'id'     => 'template-hierarchy-' . $index,
                'parent' => 'template-hierarchy',
                'title'  => ($index + 1) . '. ' . $hierarchy_item,
                'meta'   => ['class' => 'template-hierarchy-item']
            ]);
        }
    }
}

add_action('wp_footer', function () { ?>
    <script>
        document.addEventListener('wpcf7invalid', markFields);
        document.addEventListener('wpcf7submit', markFields);

        function markFields(event) {
            const fields = event.target.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], textarea');
            fields.forEach(el => {
                el.classList.remove('is-valid');
                if (!el.classList.contains('wpcf7-not-valid') && el.value.trim() !== '') {
                    el.classList.add('is-valid');
                }
            });
        }
    </script>
<?php });
