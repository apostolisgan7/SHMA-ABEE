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
$has_diagram = $diagram && is_array($diagram);
$has_left = $has_manuals || $has_diagram;

$has_tech = $has_attributes || $has_left;


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

<?php if ($product->is_type('variable') && $has_attributes) :
    $vt_base = [];
    $vt_vars = [];

    foreach ($product->get_attributes() as $attr_name => $attribute) {
        if (!$attribute->get_visible() || $attribute->get_variation()) continue;
        $label = wc_attribute_label($attribute->get_name());
        if ($attribute->is_taxonomy()) {
            $terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
            $value = implode(' / ', $terms);
        } else {
            $value = implode(' / ', $attribute->get_options());
        }
        if ($value !== '') $vt_base[] = ['label' => $label, 'value' => $value];
    }

    foreach ($product->get_available_variations() as $var) {
        $var_id = $var['variation_id'];
        $rows   = [];
        foreach ($product->get_attributes() as $attr_name => $attribute) {
            if (!$attribute->get_visible() || !$attribute->get_variation()) continue;
            $label    = wc_attribute_label($attribute->get_name());
            $attr_key = 'attribute_' . $attr_name;
            $slug     = $var['attributes'][$attr_key] ?? '';
            if ($slug === '') continue;
            if ($attribute->is_taxonomy()) {
                $term  = get_term_by('slug', $slug, $attribute->get_name());
                $value = $term ? $term->name : $slug;
            } else {
                $value = $slug;
            }
            $rows[] = ['label' => $label, 'value' => $value];
        }
        $vt_vars[$var_id] = $rows;
    }
    ?>
    <script>window.rvVariationTech = <?php echo wp_json_encode(['base' => $vt_base, 'vars' => $vt_vars]); ?>;</script>
<?php endif; ?>

<div id="tabdetails" class="rv-product-tabs" x-data="{ tab: '<?php echo $default_tab; ?>' }">

    <div class="rv-tabs-nav">
        <?php if ($has_tech): ?>
            <button @click="tab='tech'; $dispatch('rv-tab-changed')" :class="{active: tab==='tech'}">
                Τεχνικά Χαρακτηριστικά
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

            <div class="rv-tech-layout<?php echo $has_left ? '' : ' rv-tech-layout--full'; ?>">

                <?php if ($has_left): ?>
                <div class="rv-tech-left">
                    <?php if ($has_manuals): ?>
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


                    <?php if ($has_diagram) : ?>
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
                <?php endif; ?>

                <div class="rv-tech-right">

                    <?php if ($has_desc) :
                        $raw_desc = get_post_field('post_content', get_the_ID());
                    ?>
                        <div class="rv-description-block">
                            <h3><?php echo esc_html__('Περιγραφή', 'woocommerce'); ?></h3>
                            <div class="rv-description-inner">
                                <?php echo wpautop(wp_kses_post($raw_desc)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

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