<?php
if (!defined('ABSPATH'))
    exit;

class AB_Logger
{
    /**
     * Registra uma interacao vinculada a um item do banner.
     */
    public static function log($item_id, $type)
    {
        global $wpdb;

        $table_logs = Advanced_Banners_Manager::instance()->table_logs;
        $ip_address = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

        $wpdb->insert($table_logs, [
            'item_id' => (int) $item_id,
            'type' => sanitize_text_field($type),
            'ip' => $ip_address,
        ]);
    }
}
