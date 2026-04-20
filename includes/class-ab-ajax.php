<?php
if (!defined('ABSPATH'))
    exit;

class AB_Ajax
{
    public static function init()
    {
        add_action('wp_ajax_ab_save_banner', [__CLASS__, 'save_banner']);
        add_action('wp_ajax_ab_delete_banner', [__CLASS__, 'delete_banner']);
        add_action('wp_ajax_ab_duplicate_banner', [__CLASS__, 'duplicate_banner']);
        add_action('wp_ajax_ab_check_conflict', [__CLASS__, 'check_conflict']);

        // Acoes AJAX de rastreamento no front-end.
        add_action('wp_ajax_ab_track_click', [__CLASS__, 'register_click']);
        add_action('wp_ajax_nopriv_ab_track_click', [__CLASS__, 'register_click']);

        add_action('wp_ajax_ab_register_view', [__CLASS__, 'register_view']);   // alias
        add_action('wp_ajax_nopriv_ab_register_view', [__CLASS__, 'register_view']);
    }

    public static function save_banner()
    {
        check_ajax_referer('ab_nonce', 'nonce');
        if (!current_user_can('manage_options'))
            wp_send_json_error('Sem permissão');
        global $wpdb;

        $banner_id = intval($_POST['banner_id'] ?? 0);
        $name = sanitize_text_field($_POST['banner_name'] ?? '');
        $category = sanitize_text_field($_POST['banner_category'] ?? '');
        $status = intval($_POST['banner_status'] ?? 1);
        $slug = sanitize_title($name);

        $full_width = isset($_POST['banner_full_width']) ? 1 : 0;

        // Configuracoes de exibicao do banner.
        $carousel = isset($_POST['banner_carousel']) ? 1 : 0;
        $dots = isset($_POST['banner_dots']) ? 1 : 0;
        $arrows = isset($_POST['banner_arrows']) ? 1 : 0;
        $autoplay = isset($_POST['banner_autoplay']) ? 1 : 0;
        $interval_ms = intval($_POST['banner_interval'] ?? 4000);

        $table_banners = Advanced_Banners_Manager::instance()->table_banners;
        $table_items = Advanced_Banners_Manager::instance()->table_items;

        // Garante um slug unico para cada banner.
        $base_slug = $slug;
        $suffix = 2;
        while (
            $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_banners WHERE slug=%s AND id!=%d",
                $slug,
                $banner_id
            ))
        ) {
            $slug = $base_slug . '-' . $suffix;
            $suffix++;
        }

        // Cria ou atualiza o banner principal.
        if ($banner_id) {
            $wpdb->update($table_banners, [
                'name' => $name,
                'slug' => $slug,
                'category' => $category,
                'status' => $status,
                'full_width' => $full_width,
                'carousel' => $carousel,
                'dots' => $dots,
                'arrows' => $arrows,
                'autoplay' => $autoplay,
                'interval_ms' => $interval_ms,
            ], ['id' => $banner_id]);
        } else {
            $wpdb->insert($table_banners, [
                'name' => $name,
                'slug' => $slug,
                'category' => $category,
                'status' => $status,
                'full_width' => $full_width,
                'carousel' => $carousel,
                'dots' => $dots,
                'arrows' => $arrows,
                'autoplay' => $autoplay,
                'interval_ms' => $interval_ms,
            ]);
            $banner_id = $wpdb->insert_id;
        }

        // Normaliza os dados enviados pelo formulario.
        $image_ids = isset($_POST['image_id']) && is_array($_POST['image_id']) ? array_values($_POST['image_id']) : [];
        $image_ids_mobile = isset($_POST['image_id_mobile']) && is_array($_POST['image_id_mobile']) ? array_values($_POST['image_id_mobile']) : [];
        $urls = isset($_POST['url']) && is_array($_POST['url']) ? array_values($_POST['url']) : [];
        $start_dates = isset($_POST['start_date']) && is_array($_POST['start_date']) ? array_values($_POST['start_date']) : [];
        $end_dates = isset($_POST['end_date']) && is_array($_POST['end_date']) ? array_values($_POST['end_date']) : [];
        $start_times = isset($_POST['start_time']) && is_array($_POST['start_time']) ? array_values($_POST['start_time']) : [];
        $end_times = isset($_POST['end_time']) && is_array($_POST['end_time']) ? array_values($_POST['end_time']) : [];
        $statuses = isset($_POST['item_status']) && is_array($_POST['item_status']) ? array_values($_POST['item_status']) : [];
        $item_ids = isset($_POST['item_id']) && is_array($_POST['item_id']) ? array_values($_POST['item_id']) : [];
        $orderings = isset($_POST['ordering']) && is_array($_POST['ordering']) ? array_values($_POST['ordering']) : [];

        $item_ids_in_form = [];

        // Processa os itens vinculados ao banner.
        if (!empty($image_ids) || !empty($item_ids)) {
            $count = max(count($image_ids), count($item_ids));
            for ($i = 0; $i < $count; $i++) {
                $has_image = isset($image_ids[$i]) && $image_ids[$i] !== '';
                $has_item_id = isset($item_ids[$i]) && intval($item_ids[$i]) > 0;

                $itm = [
                    'banner_id' => $banner_id,
                    'image_id' => $has_image ? intval($image_ids[$i]) : 0,
                    'image_id_mobile' => isset($image_ids_mobile[$i]) ? intval($image_ids_mobile[$i]) : 0,
                    'url' => isset($urls[$i]) ? esc_url_raw($urls[$i]) : '',
                    'start_date' => isset($start_dates[$i]) && $start_dates[$i] !== '' ? $start_dates[$i] : null,
                    'end_date' => isset($end_dates[$i]) && $end_dates[$i] !== '' ? $end_dates[$i] : null,
                    'start_time' => isset($start_times[$i]) && $start_times[$i] !== '' ? $start_times[$i] : null,
                    'end_time' => isset($end_times[$i]) && $end_times[$i] !== '' ? $end_times[$i] : null,
                    'status' => isset($statuses[$i]) ? intval($statuses[$i]) : 1,
                    'ordering' => $i,
                ];

                if ($has_item_id) {
                    $wpdb->update($table_items, $itm, ['id' => intval($item_ids[$i])]);
                    $item_ids_in_form[] = intval($item_ids[$i]);
                } else {
                    if ($itm['image_id'] || $itm['image_id_mobile'] || $itm['url'] || $itm['start_date'] || $itm['end_date']) {
                        $wpdb->insert($table_items, $itm);
                        $item_ids_in_form[] = $wpdb->insert_id;
                    }
                }
            }

            // Remove itens que nao estao mais presentes no formulario.
            if (!empty($item_ids_in_form)) {
                $ids = implode(',', array_map('intval', $item_ids_in_form));
                $wpdb->query($wpdb->prepare("DELETE FROM {$table_items} WHERE banner_id=%d AND id NOT IN ({$ids})", $banner_id));
            } else {
                // Remove todos os itens quando o formulario nao envia registros.
                $wpdb->delete($table_items, ['banner_id' => $banner_id]);
            }
        }

        // Registra erros de banco para diagnostico.
        if (!empty($wpdb->last_error)) {
            error_log('AB Save Banner DB Error: ' . $wpdb->last_error);
        }

        // Indica se a operacao criou um novo banner.
        $is_new = empty($_POST['banner_id']) || intval($_POST['banner_id']) === 0;

        $response = [
            'message' => $is_new ? 'Banner criado com sucesso!' : 'Banner atualizado com sucesso!',
            'banner_id' => intval($banner_id),
            'is_new' => $is_new,
        ];

        // Retorna o ID do banner salvo para manter o formulario sincronizado.
        wp_send_json_success($response);
    }

    public static function delete_banner()
    {
        check_ajax_referer('ab_nonce', 'nonce');
        if (!current_user_can('manage_options'))
            wp_send_json_error('Sem permissão');
        global $wpdb;

        $id = intval($_POST['id']);
        $wpdb->delete(Advanced_Banners_Manager::instance()->table_banners, ['id' => $id]);
        $wpdb->delete(Advanced_Banners_Manager::instance()->table_items, ['banner_id' => $id]);

        wp_send_json_success([
            'message' => 'Banner deletado com sucesso!'
        ]);
    }

    public static function duplicate_banner()
    {
        check_ajax_referer('ab_nonce', 'nonce');
        if (!current_user_can('manage_options'))
            wp_send_json_error('Sem permissão');
        global $wpdb;

        $id = intval($_POST['id']);
        $table_banners = Advanced_Banners_Manager::instance()->table_banners;
        $table_items = Advanced_Banners_Manager::instance()->table_items;

        $b = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_banners WHERE id=%d", $id));
        if (!$b)
            wp_send_json_error('Banner não encontrado');

        $wpdb->insert($table_banners, [
            'name' => $b->name . ' (cópia)',
            'slug' => $b->slug . '-copy',
            'category' => $b->category,
            'status' => $b->status
        ]);
        $new_id = $wpdb->insert_id;

        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_items WHERE banner_id=%d", $id));
        foreach ($items as $it) {
            $wpdb->insert($table_items, [
                'banner_id' => $new_id,
                'image_id' => $it->image_id,
                'url' => $it->url,
                'start_date' => $it->start_date,
                'end_date' => $it->end_date,
                'start_time' => $it->start_time,
                'end_time' => $it->end_time,
                'status' => $it->status
            ]);
        }

        wp_send_json_success();
    }

    public static function check_conflict()
    {
        check_ajax_referer('ab_nonce', 'nonce');
        if (!current_user_can('manage_options'))
            wp_send_json_error('Sem permissão');
        wp_send_json_success(['ok' => true]);
    }

    public static function register_view()
    {
        check_ajax_referer('ab_nonce', 'nonce');

        global $wpdb;
        $item_id = intval($_POST['item_id'] ?? 0);
        if (!$item_id) {
            wp_send_json_error(['message' => 'Item inválido']);
        }

        $table_items = Advanced_Banners_Manager::instance()->table_items;
        $table_logs = Advanced_Banners_Manager::instance()->table_logs;

        $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET views = views+1 WHERE id=%d", $item_id));

        wp_send_json_success(['message' => 'View registrada']);
    }

    public static function register_click()
    {
        check_ajax_referer('ab_nonce', 'nonce');

        global $wpdb;
        $item_id = intval($_POST['item_id'] ?? 0);
        if (!$item_id) {
            wp_send_json_error(['message' => 'Item inválido']);
        }

        $table_items = Advanced_Banners_Manager::instance()->table_items;
        $table_logs = Advanced_Banners_Manager::instance()->table_logs;

        $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET clicks = clicks+1 WHERE id=%d", $item_id));

        wp_send_json_success(['message' => 'Click registrado']);
    }
}
