<?php
$big_post = get_field('first_big_post');
$bottom_posts = get_field('three_bottom_posts');

if (!$big_post) return;

$big_post = is_array($big_post) ? $big_post[0] : $big_post;
?>

<section class="home-blog-grid section-full-width">
    <div class="container">
        <div class="first_post">
            <div class="hbg-left">
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
            <div class="hbg-image">
                <?php echo get_the_post_thumbnail($big_post->ID, 'large'); ?>
            </div>
        </div>
        <!-- BOTTOM GRID -->
        <div class="bottom_grid">
            <h2 class="bottom_title"><?= __('Διαβάστε Επίσης:', 'ruined') ?></h2>

            <?php if ($bottom_posts): ?>
                <div class="hbg-bottom">
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
