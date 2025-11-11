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

                acf_register_block_type([
                    'name'              => $block_name,
                    'title'             => __($block_title, 'ruined'),
                    'render_template'   => "blocks/{$block}/block.php",
                    'category'          => 'ruined-blocks',
                    'icon'              => 'admin-comments',
                    'keywords'          => [$block_name, 'ruined'],
                    'supports'          => [
                        'align' => false,
                        'anchor' => true,
                        'customClassName' => true,
                    ],
                ]);
            }
        }
    }
}

// Initialize
new Ruined_Blocks();