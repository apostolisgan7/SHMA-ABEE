<?php
$title = get_field('faq_title');
$faqs = get_field('faqs');
?>

<section class="faq-section">
    <div class="faq-section__inner container">

        <!-- LEFT COLUMN (STICKY TITLE) -->
        <div class="faq-section__left">
            <?php if ($title): ?>
                <h2 class="faq-section__title">
                    <?= esc_html($title); ?>
                </h2>
            <?php endif; ?>
        </div>

        <!-- RIGHT COLUMN (ACCORDION) -->
        <div class="faq-section__right" x-data="{ openItem: 0 }">
            <?php if ($faqs): ?>
                <div class="faq-section__accordion">

                    <?php foreach ($faqs as $index => $row):
                        $item = $row['faq_item'];
                        $item_title = $item['title'] ?? '';
                        $item_text = $item['text'] ?? '';
                        $id = 'faq_' . $index;
                        ?>

                        <div class="faq-item"
                             x-data="{ index: <?= $index ?> }">

                            <button
                                    class="faq-item__header"
                                    @click="openItem = (openItem === index ? null : index)"
                                    :class="{ 'active': openItem === index }"
                            >
                                <span><?= esc_html($item_title); ?></span>

                                <div  class="faq-item__icon"">
                                    <svg width="12" height="8" viewBox="0 0 12 8"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.4337 0.995779L5.71489 6.14356L0.996094 0.995778" fill="white"/>
                                        <path d="M10.4337 0.995779L5.71489 6.14356L0.996094 0.995778" stroke="black"
                                              stroke-width="1.99169" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>

                            </button>

                            <!-- CONTENT -->
                            <div class="faq-item__content"
                                 x-ref="content"
                                 x-bind:style="openItem === index ?
                                 'max-height: ' + $refs.content.scrollHeight + 'px' :
                                 'max-height: 0px'">
                                <div class="faq-item__text">
                                    <?= wp_kses_post($item_text); ?>
                                </div>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>
            <?php endif; ?>
        </div>

    </div>
</section>
