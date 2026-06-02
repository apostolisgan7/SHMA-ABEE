<?php
$title    = get_field('title');
$items    = get_field('history_item');
$block_id = 'hh-' . $block['id'];
?>

<section class="history-horizontal section-full-width" id="<?php echo esc_attr($block_id); ?>">
  <?php if ($title): ?>
    <h2 class="history-horizontal__title"><?php echo esc_html($title); ?></h2>
  <?php endif; ?>

  <div class="swiper history-horizontal__swiper">
    <div class="swiper-wrapper">
      <?php if ($items): foreach ($items as $item):
        $img        = $item['image'];
        $year       = $item['year'];
        $card_title = $item['title'];
        $text       = $item['text'];
      ?>
      <div class="swiper-slide history-horizontal__slide">
        <?php if ($img): ?>
          <div class="history-horizontal__slide-img">
            <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy"/>
          </div>
        <?php endif; ?>
        <div class="history-horizontal__slide-content">
          <?php if ($year): ?>
            <p class="history-horizontal__year"><?php echo esc_html($year); ?></p>
          <?php endif; ?>
          <div class="history-horizontal__bottom">
            <?php if ($card_title): ?>
              <p class="history-horizontal__card-title" ><?php echo wp_kses_post($card_title); ?></p>
            <?php endif; ?>
            <?php if ($text): ?>
              <div class="history-horizontal__text"><?php echo wp_kses_post($text); ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <div class="history-horizontal__pagination"></div>
</section>
