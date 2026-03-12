<?php
defined('ABSPATH') || exit;

// Tech Specs group
$tech = get_field('tech_specs');

global $product;

/* --------------------------------
CHECK TECH TAB DATA
-------------------------------- */

$has_attributes = false;

if ($product) {
    $attributes = $product->get_attributes();

    foreach ($attributes as $attribute) {

        if ($attribute->is_taxonomy()) {
            $terms = wc_get_product_terms(
                    $product->get_id(),
                    $attribute->get_name(),
                    ['fields' => 'names']
            );

            if (!empty($terms)) {
                $has_attributes = true;
                break;
            }

        } else {

            if (!empty($attribute->get_options())) {
                $has_attributes = true;
                break;
            }

        }
    }
}

$manuals = get_field('manuals');
$has_manuals = !empty($manuals);
$diagram = get_field('schediagramma');

$has_tech = $has_attributes || $has_manuals || $diagram;


/* --------------------------------
CHECK DESCRIPTION TAB
-------------------------------- */

$description = trim(get_the_content());
$has_desc = !empty($description);


/* --------------------------------
CHECK PROJECTS TAB
-------------------------------- */

$projects = get_field('projects_gallery');
$has_projects = !empty($projects);


/* --------------------------------
IF NOTHING EXISTS → STOP
-------------------------------- */

if (!$has_tech && !$has_desc && !$has_projects) {
    return;
}
$default_tab = $has_tech ? 'tech' : ($has_desc ? 'desc' : 'projects');
?>

<div id="tabdetails" class="rv-product-tabs" x-data="{ tab: '<?php echo $default_tab; ?>' }">

    <div class="rv-tabs-nav">
        <?php if ($has_tech): ?>
            <button @click="tab='tech'; $dispatch('rv-tab-changed')" :class="{active: tab==='tech'}">
                Τεχνικά Χαρακτηριστικά
            </button>
        <?php endif; ?>

        <?php if ($has_desc): ?>
            <button @click="tab='desc'; $dispatch('rv-tab-changed')" :class="{active: tab==='desc'}">
                Περιγραφή Προϊόντος
            </button>
        <?php endif; ?>

        <?php if ($has_projects): ?>
            <button @click="tab='projects'; $dispatch('rv-tab-changed')" :class="{active: tab==='projects'}">
                Φωτογραφίες Έργου
            </button>
        <?php endif; ?>

    </div>
    <?php if ($has_tech): ?>
        <div x-show="tab==='tech'" x-cloak class="rv-tab-content">

            <div class="rv-tech-layout">

                <div class="rv-tech-left">
                    <?php if ($manuals): ?>
                        <div class="rv-manuals">
                            <h3>Τεχνικοί Κατάλογοι</h3>

                            <?php foreach ($manuals as $row):

                                $file  = $row['file'];
                                $title = $row['title'];

                                if (!$file) continue;
                                ?>

                                <a href="<?php echo esc_url($file['url']); ?>" target="_blank" rel="noopener">

                                    <div class="rv-manual-wrap">

                                        <div class="rv-manual-icon">
                                            <?php echo esc_html(mb_substr($title, 0, 1)); ?>
                                        </div>

                                        <div class="rv-manual-meta">
                                            <strong>Download Manual</strong>
                                            <span><?php echo esc_html($title); ?></span>
                                        </div>

                                    </div>

                                    <div class="download-arrow">
                                        <svg width="11" height="7" viewBox="0 0 11 7" fill="none">
                                            <path d="M9.55443 0.911985L5.23327 5.62598L0.912109 0.911985"
                                                  stroke="black"
                                                  stroke-width="1.82386"
                                                  stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                        </svg>
                                    </div>

                                </a>

                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>


                    <?php if ($diagram && is_array($diagram)) : ?>
                        <div class="rv-tech-diagram">
                            <h3>Σχεδιάγραμμα Προϊόντος</h3>
                            <a href="<?php echo esc_url($diagram['url']); ?>"
                               data-fancybox="product-diagram"
                               class="rv-diagram-link">
                                <img src="<?php echo esc_url($diagram['sizes']['medium']); ?>"
                                     alt="<?php echo esc_attr($diagram['alt']); ?>"/>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="rv-tech-right">

                    <?php if ($product) :

                        $attributes = $product->get_attributes();

                        if (!empty($attributes)) :
                            ?>

                            <h3>Τεχνικά Χαρακτηριστικά</h3>

                            <div class="rv-tech-table">

                                <?php foreach ($attributes as $attribute) :

                                    if (!$attribute->get_visible()) {
                                        continue;
                                    }

                                    $label = wc_attribute_label($attribute->get_name());

                                    if ($attribute->is_taxonomy()) {

                                        $terms = wc_get_product_terms(
                                                $product->get_id(),
                                                $attribute->get_name(),
                                                ['fields' => 'names']
                                        );

                                        $value = !empty($terms) ? implode(' &nbsp; ', $terms) : '';

                                    } else {

                                        $options = $attribute->get_options();
                                        $value = !empty($options) ? implode(' &nbsp; ', $options) : '';
                                    }

                                    if (empty($value)) continue;
                                    ?>

                                    <div class="row">
                                        <span><?php echo esc_html($label); ?></span>
                                        <strong><?php echo esc_html($value); ?></strong>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; endif; ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($has_desc): ?>
        <div x-show="tab==='desc'" x-cloak class="rv-tab-content">
            <div class="rv-description-inner">
                <?php the_content(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($has_projects): ?>
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
    <?php endif; ?>

</div>