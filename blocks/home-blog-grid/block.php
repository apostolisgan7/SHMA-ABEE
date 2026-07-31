<?php
/*
 * Ο πελάτης δεν έχει ακόμα blog posts. Όσο το "Manual Big Card" (τίτλος/εικόνα)
 * είναι συμπληρωμένο, δείχνουμε manual περιεχόμενο (τίτλος+εικόνα+link, χωρίς
 * category/date που δεν υπάρχουν σε manual κάρτες) αντί για πραγματικά posts.
 */
$manual_big = get_field('manual_big_card');
$use_manual = !empty($manual_big['manual_big_title']) || !empty($manual_big['manual_big_image']);

if ($use_manual) {
    $bottom_cards = get_field('manual_bottom_cards');
    $big_title = $manual_big['manual_big_title'];
    $big_image = $manual_big['manual_big_image'];
    $big_link = $manual_big['manual_big_link'];
    ?>

    <section class="home-blog-grid section-full-width">
        <div class="container smaller-container">
            <div class="first_post">
                <div class="hbg-left" data-animate="fade-up">
                    <?php if (!empty($manual_big['tag']) || !empty($manual_big['date'])): ?>
                        <div class="post_head">
                            <span class="hbg-category"><?php echo esc_html($manual_big['tag']); ?></span>
                            <span class="hbg-date"><?php echo esc_html($manual_big['date']); ?></span>
                        </div>
                    <?php endif; ?>
                    <h2 class="hbg-title">
                        <?php echo esc_html($big_title); ?>
                    </h2>

                    <?php if ($big_link && !empty($big_link['url'])):
                        rv_button_arrow([
                                'text' => __('Δείτε περισσότερα', 'ruined'),
                                'url' => $big_link['url'],
                                'target' => $big_link['target'] ?: '_self',
                                'variant' => 'white',
                                'icon_position' => 'left',
                                'class' => 'blog-grid__btn',
                                'register' => false,
                        ]);
                    endif; ?>
                </div>
                <div class="hbg-image" data-animate="image-reveal" data-animate-direction="right">
                    <?php if ($big_image): ?>
                        <img src="<?php echo esc_url($big_image['url']); ?>" alt="<?php echo esc_attr($big_image['alt']); ?>">
                    <?php endif; ?>
                </div>
            </div>
            <!-- BOTTOM GRID -->
            <div class="bottom_grid">
                <h2 class="bottom_title"><?= __('Διαβάστε Επίσης:', 'ruined') ?></h2>

                <?php if ($bottom_cards): ?>
                    <div class="hbg-bottom" data-animate="stagger-fade" data-animate-stagger="0.12">
                        <?php foreach ($bottom_cards as $card):
                            $card_url = !empty($card['link']) ? $card['link'] : '#';
                            ?>
                            <a class="hbg-card" href="<?php echo esc_url($card_url); ?>">

                                <div class="hbg-card-thumb">
                                    <?php if (!empty($card['image'])): ?>
                                        <img src="<?php echo esc_url($card['image']['url']); ?>" alt="<?php echo esc_attr($card['image']['alt']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="hbg-card-info">
                                    <?php if (!empty($card['tag']) || !empty($card['date'])): ?>
                                        <div class="hbg-card-meta">
                                            <span class="hbg-card-category"><?php echo esc_html($card['tag']); ?></span>
                                            <span class="hbg-card-date"><?php echo esc_html($card['date']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="hbg-card-title">
                                        <?php echo esc_html($card['title']); ?>
                                    </h3>
                                    <div class="card__line"></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    return;
}

$big_post = get_field('first_big_post');
$bottom_posts = get_field('three_bottom_posts');

if (!$big_post) return;

$big_post = is_array($big_post) ? $big_post[0] : $big_post;
?>

<section class="home-blog-grid section-full-width">
    <div class="container smaller-container">
        <div class="first_post">
            <div class="hbg-left" data-animate="fade-up">
                <div class="post_head">
        <span class="hbg-category">
            <?php echo get_the_category($big_post->ID)[0]->name; ?>
        </span>
                    <span class="hbg-date">
            <?php echo get_the_date('F j, Y', $big_post->ID); ?>
        </span>
                </div>
                <h2 class="hbg-title">
                    <?php echo get_the_title($big_post->ID); ?>
                </h2>

                <?php
                $btn_url = get_permalink($big_post->ID);
                rv_button_arrow([
                        'text' => __('Δείτε περισσότερα', 'ruined'),
                        'url' => $btn_url,
                        'target' => '_self',
                        'variant' => 'white',
                        'icon_position' => 'left',
                        'class' => 'blog-grid__btn',
                        'register' => false,
                ]);
                ?>
            </div>
            <div class="hbg-image" data-animate="image-reveal" data-animate-direction="right">
                <?php echo get_the_post_thumbnail($big_post->ID, 'large'); ?>
            </div>
        </div>
        <!-- BOTTOM GRID -->
        <div class="bottom_grid">
            <h2 class="bottom_title"><?= __('Διαβάστε Επίσης:', 'ruined') ?></h2>

            <?php if ($bottom_posts): ?>
                <div class="hbg-bottom" data-animate="stagger-fade" data-animate-stagger="0.12">
                    <?php foreach ($bottom_posts

                                   as $post): ?>
                        <a class="hbg-card" href="<?php echo get_permalink($post->ID); ?>">

                            <div class="hbg-card-thumb">
                                <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                            </div>
                            <div class="hbg-card-info">
                                <div class="hbg-card-meta">
                    <span class="hbg-card-category">
                        <?php echo get_the_category($post->ID)[0]->name; ?>
                    </span>
                                    <span class="hbg-card-date">
                        <?php echo get_the_date('F j, Y', $post->ID); ?>
                    </span>
                                </div>
                                <h3 class="hbg-card-title">
                                    <?php echo get_the_title($post->ID); ?>
                                </h3>
                                <div class="card__line"></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
