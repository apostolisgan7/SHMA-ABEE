<?php
/**
 * Block: Home History
 */

$block_id    = $block['id'] ?? '';
$block_class = 'home-history';
if ( ! empty( $block['className'] ) ) {
    $block_class .= ' ' . $block['className'];
}

$top_row    = get_field( 'top_row' );
$bottom_row = get_field( 'bottom_row' );

$top_text   = $top_row['text'] ?? '';
$top_image  = $top_row['image'] ?? null;

$bottom_image  = $bottom_row['image'] ?? null;
$counter_items = $bottom_row['counter_items'] ?? [];
$bottom_text   = $bottom_row['text'] ?? '';
$button_link   = $bottom_row['button_link'] ?? null;
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $block_class ); ?> section-full-width">
    <div class="home-history__inner container">
        <div class="home-history__top">
            <?php if ( ! empty( $top_text ) ) : ?>
                <div class="home-history__top-text">
                    <?php echo wp_kses_post( $top_text ); ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $top_image ) ) : ?>
                <div class="home-history__top-media">
                    <?php
                    echo wp_get_attachment_image(
                        $top_image['ID'] ?? $top_image,
                        'large',
                        false,
                        array(
                            'class' => 'home-history__top-img',
                            'loading' => 'lazy',
                        )
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="home-history__bottom">
            <?php if ( ! empty( $bottom_image ) ) : ?>
                <div class="home-history__bottom-media">
                    <?php
                    echo wp_get_attachment_image(
                        $bottom_image['ID'] ?? $bottom_image,
                        'large',
                        false,
                        array(
                            'class' => 'home-history__bottom-img',
                            'loading' => 'lazy',
                        )
                    );
                    ?>
                </div>
            <?php endif; ?>

            <div class="home-history__bottom-content">
                <?php if ( ! empty( $counter_items ) ) : ?>
                    <div class="home-history__counters">
                        <?php foreach ( $counter_items as $item_row ) :
                            $item = $item_row['item'] ?? [];
                            $title = $item['title'] ?? '';
                            $number = $item['number'] ?? '';
                            ?>
                            <div class="home-history__counter">
                                <?php if ( $title ) : ?>
                                    <p class="home-history__counter-label"><?php echo esc_html( $title ); ?></p>
                                <?php endif; ?>
                                <?php if ( $number ) : ?>
                                    <p class="home-history__counter-number"><?php echo esc_html( $number ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $bottom_text ) ) : ?>
                    <div class="home-history__text">
                        <?php echo wp_kses_post( $bottom_text ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $button_link ) ) :
                    $btn_url     = $button_link['url'] ?? '#';
                    $btn_title   = $button_link['title'] ?? '';
                    $btn_target  = $button_link['target'] ?? '_self';

                    rv_button_arrow( [
                        'text'          => $btn_title,
                        'url'           => $btn_url,
                        'target'        => $btn_target,
                        'variant'       => 'black',
                        'icon_position' => 'left',
                        'class'         => 'home-history__btn',
                        'register'      => false,
                    ] );
                endif; ?>
            </div>
        </div>
    </div>
</section>
