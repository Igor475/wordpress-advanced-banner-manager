<?php
if (!defined('ABSPATH'))
    exit;

class AB_Activator
{
    /**
     * Cria ou atualiza as tabelas principais do plugin.
     */
    public static function activate()
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table_banners = $wpdb->prefix . 'ab_banners';
        $table_items = $wpdb->prefix . 'ab_items';
        $table_logs = $wpdb->prefix . 'ab_logs';

        $queries = [
            "CREATE TABLE {$table_banners} (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                slug varchar(191) NOT NULL,
                category varchar(200) DEFAULT '',
                status tinyint(1) DEFAULT 1,
                ordering int DEFAULT 0,
                full_width tinyint(1) DEFAULT 0,
                carousel tinyint(1) DEFAULT 0,
                dots tinyint(1) DEFAULT 1,
                arrows tinyint(1) DEFAULT 1,
                autoplay tinyint(1) DEFAULT 1,
                interval_ms int DEFAULT 4000,
                views bigint(20) DEFAULT 0,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY slug (slug)
            ) {$charset_collate};",
            "CREATE TABLE {$table_items} (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                banner_id mediumint(9) NOT NULL,
                image_id bigint(20) NOT NULL,
                image_id_mobile bigint(20) DEFAULT NULL,
                url varchar(255) DEFAULT '',
                start_date date DEFAULT NULL,
                end_date date DEFAULT NULL,
                start_time time DEFAULT NULL,
                end_time time DEFAULT NULL,
                status tinyint(1) DEFAULT 1,
                clicks bigint(20) DEFAULT 0,
                views bigint(20) DEFAULT 0,
                ordering int DEFAULT 0,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY banner_id (banner_id)
            ) {$charset_collate};",
            "CREATE TABLE {$table_logs} (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                item_id bigint(20) NOT NULL,
                type varchar(20) NOT NULL,
                ip varchar(45) DEFAULT '',
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY item_id (item_id)
            ) {$charset_collate};",
        ];

        foreach ($queries as $sql) {
            dbDelta($sql);
        }

        if (!empty($wpdb->last_error)) {
            error_log('Advanced Banners DB Error: ' . $wpdb->last_error);
        }
    }
}
