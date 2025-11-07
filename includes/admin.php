<?php

// --- Admin Customizations ---

// Example: Customize the admin footer text
add_filter('admin_footer_text', function() {
    echo 'Theme developed by <a href="#" target="_blank">Ruined Visuals</a>.';
});

/**
 * Display current template name in admin bar
 */
function ruined_show_template_name_in_admin_bar($wp_admin_bar) {
    if (!is_admin() && current_user_can('manage_options')) {
        global $template;
        $template_name = basename($template);
        $template_path = str_replace(ABSPATH, '', $template);

        $wp_admin_bar->add_node([
            'id'    => 'current-template',
            'title' => 'Template: ' . $template_name,
            'parent' => 'top-secondary',
            'href'   => admin_url('theme-editor.php?file=' . $template_path),
            'meta'   => [
                'class' => 'current-template',
                'title' => 'Edit this template',
            ],
        ]);
    }
}
add_action('admin_bar_menu', 'ruined_show_template_name_in_admin_bar', 100);

// Add some styles for the admin bar item
function ruined_admin_bar_styles() {
    if (is_admin_bar_showing()) {
        echo '<style>
            #wpadminbar #wp-admin-bar-current-template > .ab-item {
                background: rgba(255,255,255,0.2) !important;
                color: #fff !important;
            }
            #wpadminbar #wp-admin-bar-current-template > .ab-item:hover {
                background: rgba(255,255,255,0.3) !important;
            }
        </style>';
    }
}
add_action('wp_head', 'ruined_admin_bar_styles');
add_action('admin_head', 'ruined_admin_bar_styles');
