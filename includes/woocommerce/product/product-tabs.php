<?php
defined('ABSPATH') || exit;

// Tech Specs group
$tech = get_field('tech_specs');
?>

<div id="tabdetails" class="rv-product-tabs" x-data="{ tab: 'tech' }">

    <div class="rv-tabs-nav">
        <button @click="tab='tech'; $dispatch('rv-tab-changed')" :class="{active: tab==='tech'}">
            Τεχνικά Χαρακτηριστικά
        </button>
        <button @click="tab='desc'; $dispatch('rv-tab-changed')" :class="{active: tab==='desc'}">
            Περιγραφή Προϊόντος
        </button>
        <button @click="tab='projects'; $dispatch('rv-tab-changed')" :class="{active: tab==='projects'}">
            Φωτογραφίες Έργου
        </button>
    </div>

    <div x-show="tab==='tech'" x-cloak class="rv-tab-content">

        <div class="rv-tech-layout">

            <div class="rv-tech-left">

                <?php if (have_rows('manuals')) : ?>
                    <div class="rv-manuals">
                        <h3>Τεχνικοί Κατάλογοι</h3>

                        <?php while (have_rows('manuals')) : the_row();
                            $file = get_sub_field('file');
                            ?>
                            <?php if ($file) : ?>
                                <a href="<?php echo esc_url($file['url']); ?>" target="_blank" rel="noopener">
                                    <div class="rv-manual-wrap">
                                        <div class="rv-manual-icon">
                                            <?php echo esc_html(mb_substr(get_sub_field('title'), 0, 1)); ?>
                                        </div>
                                        <div class="rv-manual-meta">
                                            <strong>Download Manual</strong>
                                            <span><?php echo esc_html(get_sub_field('title')); ?></span>
                                        </div>
                                    </div>
                                    <div class="download-arrow">
                                        <svg width="11" height="7" viewBox="0 0 11 7" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.55443 0.911985L5.23327 5.62598L0.912109 0.911985" stroke="black"
                                                  stroke-width="1.82386" stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endwhile; ?>

                    </div>
                <?php endif; ?>

                <?php
                $diagram = get_field('schediagramma');
                ?>
                <?php if ($diagram && is_array($diagram)) : ?>
                    <div class="rv-tech-diagram">
                        <h3>Σχεδιάγραμμα Προϊόντος</h3>
                        <a href="<?php echo esc_url($diagram['url']); ?>"
                           data-fancybox="product-diagram"
                           class="rv-diagram-link">
                            <img src="<?php echo esc_url($diagram['sizes']['medium']); ?>"
                                 alt="<?php echo esc_attr($diagram['alt']); ?>" />
                        </a>
                    </div>
                <?php endif; ?>

            </div>

            <div class="rv-tech-right">

                <h3>Τεχνικά Χαρακτηριστικά</h3>

                <?php if ($tech) : ?>
                    <div class="rv-tech-table">

                        <?php if (!empty($tech['material'])) : ?>
                            <div class="row">
                                <span>Υλικό Κατασκευής</span>
                                <strong><?php echo esc_html($tech['material']); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['thickness'])) : ?>
                            <div class="row">
                                <span>Πάχος Κατασκευής</span>
                                <strong><?php echo esc_html(implode(' &nbsp; ', $tech['thickness'])); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['sign_shape'])) : ?>
                            <div class="row">
                                <span>Σχήμα Σήμανσης</span>
                                <strong><?php echo esc_html($tech['sign_shape']); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['dimensions'])) : ?>
                            <div class="row">
                                <span>Διαστάσεις</span>
                                <strong><?php echo esc_html(implode(' &nbsp; ', $tech['dimensions'])); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['reflectivity'])) : ?>
                            <div class="row">
                                <span>Ανακλαστικότητα</span>
                                <strong><?php echo esc_html(implode(' &nbsp; ', $tech['reflectivity'])); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['specs_standard'])) : ?>
                            <div class="row">
                                <span>Τεχνικές προδιαγραφές από</span>
                                <strong><?php echo esc_html($tech['specs_standard']); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['manufacturer'])) : ?>
                            <div class="row">
                                <span>Κατασκευαστής</span>
                                <strong><?php echo esc_html($tech['manufacturer']); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tech['certifications'])) : ?>
                            <div class="row">
                                <span>Πιστοποιήσεις</span>
                                <strong>
                                    <?php
                                    $certs = wp_list_pluck($tech['certifications'], 'certification');
                                    echo esc_html(implode(' &nbsp; ', $certs));
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>


    <div x-show="tab==='desc'" x-cloak class="rv-tab-content">
        <div class="rv-description-inner">
            <?php the_content(); ?>
        </div>
    </div>

    <div x-show="tab==='projects'" x-cloak class="rv-tab-content" id="rv-projects-tab">
        <?php 
        $projects = get_field('projects_gallery');
        if ($projects) : 
            // Convert single value to array for consistent processing
            $projects = is_array($projects) ? $projects : [$projects];
            ?>
            <div class="rv-gallery">
                <?php foreach ($projects as $img) : 
                    // Handle both array and ID formats
                    $img_id = is_array($img) ? $img['ID'] : $img;
                    if (!$img_id) continue;
                    ?>
                    <a href="<?php echo wp_get_attachment_image_url($img_id, 'medium_large'); ?>"
                       data-fancybox="project-gallery"
                       class="rv-project-thumb">
                        <?php echo wp_get_attachment_image(
                            $img_id,
                            'medium',
                            false,
                            ['class' => 'rv-project-image']
                        ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>