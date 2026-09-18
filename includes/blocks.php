<?php
/**
 * Blocks functionality
 *
 * @package Ruined
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Ruined_Blocks {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('acf/init', [$this, 'register_blocks']);
        add_filter('block_categories_all', [$this, 'register_block_category'], 10, 2);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_placeholder_styles']);
    }

    /**
     * Register custom block category
     */
    public function register_block_category($categories, $post) {
        return array_merge(
            $categories,
            [
                [
                    'slug'  => 'ruined-blocks',
                    'title' => __('Shma Blocks', 'ruined'),
                    'icon'  => 'wordpress',
                ],
            ]
        );
    }

    /**
     * Register ACF blocks
     */
    public function register_blocks() {
        // Check if ACF is installed
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        // Define blocks directory
        $blocks_dir = get_template_directory() . '/blocks';

        // Check if directory exists
        if (!is_dir($blocks_dir)) {
            return;
        }

        // Scan blocks directory
        $blocks = array_diff(scandir($blocks_dir), ['..', '.']);

        foreach ($blocks as $block) {
            $block_path = $blocks_dir . '/' . $block;

            // Only process directories
            if (!is_dir($block_path)) {
                continue;
            }

            // Check if block.json exists
            $block_json = $block_path . '/block.json';

            if (file_exists($block_json)) {
                // Register block using block.json
                register_block_type($block_path);
            } else {
                // Fallback: Register using PHP (for backward compatibility)
                $block_name = str_replace('-', '_', $block);
                $block_title = ucwords(str_replace('-', ' ', $block));

                // ACF Blocks V3 for every block: compact editor placeholder via
                // the shared render_callback, fields edited only through the
                // Expanded Editor. 'mode' is a V1/V2-only setting and has no
                // effect once acf_block_version is 3, so it's omitted.
                $block_args = [
                    'name'                  => $block_name,
                    'title'                 => __($block_title, 'ruined'),
                    'render_template'       => "blocks/{$block}/block.php",
                    'render_callback'       => [$this, 'render_block'],
                    'category'              => 'ruined-blocks',
                    'icon'                  => 'admin-comments',
                    'keywords'              => [$block_name, 'ruined'],
                    'acf_block_version'     => 3,
                    'hide_fields_in_sidebar' => true,
                    'auto_inline_editing'   => false,
                    'supports'              => [
                        'align' => false,
                        'anchor' => true,
                        'customClassName' => true,
                    ],
                ];

                acf_register_block_type($block_args);
            }
        }
    }

    /**
     * Shared render_callback for all PHP-registered ACF blocks.
     *
     * $is_preview is supplied by ACF itself and is true only inside the
     * block editor's own preview/REST render — never on the frontend.
     */
    public function render_block($block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = false) {
        if ($is_preview) {
            printf(
                '<div class="ruined-block-placeholder">%s</div>',
                esc_html($block['title'])
            );
            return;
        }

        // Same file-resolution order ACF's own acf_block_render_template() uses.
        if (!empty($block['path']) && file_exists($block['path'] . '/' . $block['render_template'])) {
            $path = $block['path'] . '/' . $block['render_template'];
        } elseif (file_exists($block['render_template'])) {
            $path = $block['render_template'];
        } else {
            $path = locate_template($block['render_template']);
        }

        if ($path) {
            include $path;
        }
    }

    /**
     * Editor-only styling for the compact block placeholder.
     */
    public function enqueue_block_placeholder_styles() {
        wp_add_inline_style('wp-edit-blocks', '
            .ruined-block-placeholder {
                display: flex;
                align-items: center;
                min-height: 60px;
                padding: 0 16px;
                background: #f0f0f1;
                border: 1px dashed #c3c4c7;
                border-radius: 2px;
                font-weight: 600;
                font-size: 13px;
            }
        ');
    }
}

// Initialize
new Ruined_Blocks();