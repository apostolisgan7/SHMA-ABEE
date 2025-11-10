<?php
function rv_button_arrow( $args = [] ) {
    // default values
    $defaults = [
        'text'          => 'Read more',
        'url'           => '#',
        'target'        => '_self',
        'variant'       => 'black', // black | white
        'icon_position' => 'left',  // left | right
        'class'         => '',      // έξτρα κλάσεις αν θες
        'register'      => true,    // για WPML string register
    ];

    $args = wp_parse_args( $args, $defaults );

    // WPML / Polylang string registration
    // αν έχεις WPML:
    if ( function_exists( 'icl_register_string' ) && $args['register'] ) {
        icl_register_string( 'ruined-buttons', 'button_text_' . $args['text'], $args['text'] );
        $args['text'] = icl_t( 'ruined-buttons', 'button_text_' . $args['text'], $args['text'] );
    }
    // αν έχεις Polylang:
    if ( function_exists( 'pll__' ) ) {
        $args['text'] = pll__( $args['text'] );
    }

    // classes
    $classes   = ['button-arrow', 'button-arrow--' . $args['variant']];
    if ( ! empty( $args['class'] ) ) {
        $classes[] = $args['class'];
    }

    // icon html (ίδιο με αυτό που φτιάξαμε)
    $icon_html = '
        <span class="button-arrow__icon">
            <span class="button-arrow__arrow button-arrow__arrow--front"></span>
            <span class="button-arrow__arrow button-arrow__arrow--back"></span>
            <span class="button-arrow__fill"></span>
        </span>
    ';

    // text html
    $text_html = '<span class="button-arrow__text">' . esc_html( $args['text'] ) . '</span>';

    // icon left or right
    if ( $args['icon_position'] === 'right' ) {
        $inner = $text_html . $icon_html;
    } else {
        $inner = $icon_html . $text_html;
    }

    echo '<a href="' . esc_url( $args['url'] ) . '" target="' . esc_attr( $args['target'] ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">'
        . $inner .
        '</a>';
}
