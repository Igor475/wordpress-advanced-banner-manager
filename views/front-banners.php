<?php if (!defined('ABSPATH'))
    exit; ?>

<?php
// Ordena os itens antes da renderizacao.
usort($valid, function ($a, $b) {
    return intval($a->ordering) <=> intval($b->ordering);
});

// Carrega os dados do banner com prioridade para o ID.
if (!empty($atts['id'])) {
    $banner = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_banners WHERE id = %d LIMIT 1",
        intval($atts['id'])
    ));
} else {
    $banner = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_banners WHERE slug = %s LIMIT 1",
        $atts['slug']
    ));
}

// Resolve as configuracoes finais de exibicao do banner.
$full_width = isset($banner->full_width) && $banner->full_width == 1 ? ' ab-full-width' : '';

$carousel = isset($atts['carousel']) && $atts['carousel'] !== null
    ? $atts['carousel']
    : ($banner && $banner->carousel ? 'true' : 'false');

$dots = isset($atts['dots']) && $atts['dots'] !== null
    ? $atts['dots']
    : ($banner && $banner->dots ? 'true' : 'false');

$arrows = isset($atts['arrows']) && $atts['arrows'] !== null
    ? $atts['arrows']
    : ($banner && $banner->arrows ? 'true' : 'false');

$autoplay = isset($atts['autoplay']) && $atts['autoplay'] !== null
    ? $atts['autoplay']
    : ($banner && $banner->autoplay ? 'true' : 'false');

$interval = isset($atts['interval']) && $atts['interval'] !== null
    ? intval($atts['interval'])
    : ($banner && $banner->interval_ms ? intval($banner->interval_ms) : 5000);
?>

<div class="ab-banners <?php echo ($carousel === 'true' ? 'ab-carousel' : 'ab-static'); ?><?php echo $full_width; ?>"
    data-autoplay="<?php echo esc_attr($autoplay); ?>" data-interval="<?php echo intval($interval); ?>"
    data-arrows="<?php echo esc_attr($arrows); ?>" data-dots="<?php echo esc_attr($dots); ?>">

    <div class="ab-slides">
        <?php foreach ($valid as $i => $v):
            $img = wp_get_attachment_image_src($v->image_id, 'full');
            $img_mobile = !empty($v->image_id_mobile) ? wp_get_attachment_image_src($v->image_id_mobile, 'full') : null;
            if (!$img)
                continue;
            $is_active = $i === 0 ? ' active' : '';
            ?>
            <div class="ab-banner-item<?php echo $is_active; ?>" data-index="<?php echo esc_attr($i); ?>"
                data-id="<?php echo esc_attr($v->id); ?>">

                <?php if ($v->url): ?>
                    <a href="<?php echo esc_url($v->url); ?>" class="ab-click" data-id="<?php echo esc_attr($v->id); ?>"
                        target="_blank" rel="noopener">
                        <picture>
                            <?php if ($img_mobile): ?>
                                <source srcset="<?php echo esc_url($img_mobile[0]); ?>" media="(max-width: 660px)">
                            <?php endif; ?>
                            <img src="<?php echo esc_url($img[0]); ?>" alt="" width="100%" height="492">
                        </picture>
                    </a>
                <?php else: ?>
                    <picture>
                        <?php if ($img_mobile): ?>
                            <source srcset="<?php echo esc_url($img_mobile[0]); ?>" media="(max-width: 660px)">
                        <?php endif; ?>
                        <img src="<?php echo esc_url($img[0]); ?>" alt="" width="100%" height="492">
                    </picture>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($arrows === 'true'): ?>
        <button type="button" class="ab-prevx" aria-label="Anterior">&lt;</button>
        <button type="button" class="ab-nextx" aria-label="Próximo">&gt;</button>
    <?php endif; ?>

    <?php if ($dots === 'true' && count($valid) > 1): ?>
        <div class="ab-dotsx" role="tablist">
            <?php for ($i = 0; $i < count($valid); $i++): ?>
                <span class="ab-dotx <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>" role="button"
                    aria-label="Ir para slide <?php echo $i + 1; ?>"></span>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>