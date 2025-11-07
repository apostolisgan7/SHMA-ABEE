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

        // Register Hero Block
        acf_register_block_type([
            'name'              => 'hero',
            'title'             => __('Hero', 'ruined'),
            'description'       => __('A custom hero block with title, subtitle, and button.', 'ruined'),
            'render_template'   => 'blocks/hero/block.php',
            'category'          => 'ruined-blocks',
            'icon'              => 'cover-image',
            'keywords'          => ['hero', 'banner', 'header'],
            'supports'          => [
                'align' => false,
                'anchor' => true,
                'customClassName' => true,
            ],
        ]);
        
        // Get all other blocks from the blocks directory (excluding hero)
        $blocks_dir = get_template_directory() . '/blocks';
        $exclude = ['hero'];
        
        if (!file_exists($blocks_dir)) {
            return;
        }

        $block_folders = array_diff(scandir($blocks_dir), ['..', '.']);

        foreach ($block_folders as $block_folder) {
            $block_path = $blocks_dir . '/' . $block_folder;
            $block_json = $block_path . '/block.json';

            // Check if block.json exists
            if (!file_exists($block_json)) {
                continue;
            }

            // Register the block
            register_block_type($block_path);

            // Register ACF fields if ACF is active
            $field_group = [
                'key' => 'group_block_' . $block_folder,
                'title' => 'Block: ' . ucfirst(str_replace('-', ' ', $block_folder)),
                'fields' => [],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/' . $block_folder,
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ];

            // Include block's ACF fields if exists
            $acf_fields_file = $block_path . '/acf-fields.php';
            if (file_exists($acf_fields_file)) {
                $field_group['fields'] = include $acf_fields_file;
            }

            // Register ACF fields
            if (function_exists('acf_add_local_field_group')) {
                acf_add_local_field_group($field_group);
            }
        }
    }
}

// Initialize
new Ruined_Blocks();
