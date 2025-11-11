<?php
/**
 * Admin customizations
 */

// Forcefully add the Featured Image meta box for posts and pages
add_action('admin_head', 'ruined_force_featured_image_metabox');

function ruined_force_featured_image_metabox() {
    global $post_type;
    
    // Only run on post and page edit screens
    if (!in_array($post_type, ['post', 'page'])) {
        return;
    }
    
    // Add support for post thumbnails for this post type
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
        'default'
    );
    
    // Ensure the meta box is not hidden
    $hidden_meta_boxes = get_user_meta(get_current_user_id(), 'metaboxhidden_' . $post_type, true);
    if (is_array($hidden_meta_boxes)) {
        $hidden_meta_boxes = array_diff($hidden_meta_boxes, ['postimagediv']);
        update_user_meta(get_current_user_id(), 'metaboxhidden_' . $post_type, $hidden_meta_boxes);
    }
}

// Add admin notice if featured image support is missing
add_action('admin_notices', 'ruined_check_featured_image_support');

function ruined_check_featured_image_support() {
    global $post_type;
    
    if (in_array($post_type, ['post', 'page']) && !post_type_supports($post_type, 'thumbnail')) {
        echo '<div class="notice notice-error">';
        echo '<p><strong>Warning:</strong> Featured Image support is missing for ' . $post_type . '. Please check your theme settings.</p>';
        echo '</div>';
    }
}
