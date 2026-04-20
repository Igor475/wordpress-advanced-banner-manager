<?php
if (!defined('ABSPATH'))
    exit;

class AB_Shortcode
{
    public static function render($atts)
    {
        global $wpdb;
        $atts = shortcode_atts([
            'slug' => '',
            'id' => 0,
            'carousel' => null,
            'autoplay' => null,
            'interval' => null,
            'arrows' => null,
            'dots' => null,
            'count' => 0
        ], $atts, 'banners');

        $table_items = Advanced_Banners_Manager::instance()->table_items;
        $table_banners = Advanced_Banners_Manager::instance()->table_banners;

        $now_date = current_time('Y-m-d');
        $now_time = current_time('H:i:s');

        $banner = null;

        // Prioriza a busca por ID quando informado.
        if (!empty($atts['id'])) {
            $banner = $wpdb->get_row($wpdb->prepare("
                SELECT * FROM $table_banners 
                WHERE id=%d AND status=1 
                LIMIT 1
            ", intval($atts['id'])));

            // Incrementa as visualizacoes do banner carregado.
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_banners SET views = views + 1 WHERE id = %d",
                $banner->id
            ));
        }

        // Usa o slug como alternativa quando o ID nao e informado.
        if (!$banner && !empty($atts['slug'])) {
            $slug = sanitize_text_field($atts['slug']);
            $banner = $wpdb->get_row($wpdb->prepare("
                SELECT * FROM $table_banners 
                WHERE slug=%s AND status=1 
                LIMIT 1
            ", $slug));
        }

        if (!$banner) {
            return '';
        }

        // Usa as configuracoes persistidas para montar a exibicao.
        $atts['carousel'] = !empty($banner->carousel) ? 'true' : 'false';
        $atts['autoplay'] = !empty($banner->autoplay) ? 'true' : 'false';
        $atts['interval'] = !empty($banner->interval_ms) && $banner->interval_ms > 0
            ? intval($banner->interval_ms)
            : 5000;
        $atts['arrows'] = !empty($banner->arrows) ? 'true' : 'false';
        $atts['dots'] = !empty($banner->dots) ? 'true' : 'false';

        // Busca os itens ativos associados ao banner.
        $items = $wpdb->get_results($wpdb->prepare("
            SELECT i.* FROM $table_items i
            WHERE i.banner_id=%d AND i.status=1
            ORDER BY i.ordering ASC
        ", $banner->id));

        $valid = [];
        foreach ($items as $it) {
            $ok = true;
            if ($it->start_date && $it->start_date > $now_date)
                $ok = false;
            if ($it->end_date && $it->end_date < $now_date)
                $ok = false;
            if ($ok && $it->start_time && $it->end_time && $it->start_date == $now_date && $it->end_date == $now_date) {
                if ($it->start_time > $now_time || $it->end_time <= $now_time)
                    $ok = false;
            }
            if ($ok)
                $valid[] = $it;
        }

        if (empty($valid))
            return '';

        if (intval($atts['count']) > 0) {
            $valid = array_slice($valid, 0, intval($atts['count']));
        }

        ob_start();
        $banner_ref = $banner; // Mantem referencia do banner para a view.
        include __DIR__ . '/../views/front-banners.php';
        return ob_get_clean();
    }
}

