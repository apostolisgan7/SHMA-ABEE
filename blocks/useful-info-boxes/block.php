<?php
$title = get_field('title');
$text = get_field('text');
$boxes = get_field('info_boxes');
?>

<section class="useful-info-boxes">
    <div class="container">
        <div class="useful-info-boxes-head">
            <?php if ($title) : ?>
                <h2 class="uiboxes-title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($text) : ?>
                <p class="uiboxes-text"><?php echo esc_html($text); ?></p>
            <?php endif; ?>
        </div>
        <?php if ($boxes) : ?>
            <div class="uiboxes-grid">

                <?php foreach ($boxes as $row) :
                    $box = $row['box'];
                    $b_title = $box['title'];
                    $b_image = $box['background_image'];
                    $b_link = $box['link'];

                    $bg = $b_image ? $b_image['url'] : '';
                    $url = $b_link ? $b_link['url'] : '#';
                    $label = $b_link ? $b_link['title'] : 'Δείτε περισσότερα';
                    ?>
                    <a href="<?php echo esc_url($url); ?>" class="uibox"
                       style="background-image: url('<?php echo esc_url($bg); ?>');">

                        <div class="uibox-inner">
                            <?php if ($b_title) : ?>
                                <h3><?php echo esc_html($b_title); ?></h3>
                            <?php endif; ?>

                            <div class="uibox-link">
                                <?php echo esc_html($label); ?>
                               <div class="btn_icon">
                                   <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M0.995839 0.996003L6.14362 5.7148L0.995839 10.4336" stroke="white" stroke-width="1.99169" stroke-linecap="round" stroke-linejoin="round"/>
                                   </svg>

                               </div>

                            </div>
                        </div>

                    </a>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

    </div>
</section>
