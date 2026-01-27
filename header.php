<?php
/**
 * The header template file
 *
 * @package Ruined
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full scroll-smooth">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class('text-dark-900'); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/menus/mobile-menu' ); ?>

<div id="page" class="site flex flex-col min-h-screen">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'ruined'); ?></a>

    <?php
    // Get the selected header style from customizer
    $header_style = get_theme_mod('ruined_header_style', 'shop');

    // Include the selected header template
    get_template_part('template-parts/header/header', $header_style);
    ?>

    <main id="content" class="site-content flex-grow">
        <div class="container mx-auto ">
