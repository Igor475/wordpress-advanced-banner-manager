<?php
if (!defined('ABSPATH'))
    exit;

class AB_Admin
{
    public static function menu()
    {
        add_menu_page(
            'Banners Avançados',
            'Banners',
            'manage_options',
            'ab_banners',
            [__CLASS__, 'page_list'],
            'dashicons-format-image',
            56
        );
        add_submenu_page(
            'ab_banners',
            'Novo Banner',
            'Adicionar',
            'manage_options',
            'ab_banners_new',
            [__CLASS__, 'page_edit']
        );
    }

    public static function assets($hook)
    {
        if (strpos($hook, 'ab_banners') === false)
            return;

        wp_enqueue_media();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script(
            'jquery-ui-timepicker',
            'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js',
            ['jquery', 'jquery-ui-datepicker'],
            '1.3.5',
            true
        );
        wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', [], '1.12.1');
        wp_enqueue_style('timepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css', [], '1.3.5');

        wp_enqueue_script(
            'ab-admin-js',
            plugin_dir_url(__DIR__) . 'assets/js/admin.js',
            ['jquery'],
            Advanced_Banners_Manager::instance()->version,
            true
        );
        wp_enqueue_style(
            'ab-admin-css',
            plugin_dir_url(__DIR__) . 'assets/css/admin.css',
            [],
            Advanced_Banners_Manager::instance()->version
        );

        // Dados AJAX utilizados no painel administrativo.
        wp_localize_script('ab-admin-js', 'AB_Admin_Ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ab_nonce')
        ]);
    }

    public static function page_list()
    {
        global $wpdb;
        $table = Advanced_Banners_Manager::instance()->table_banners;
        $banners = $wpdb->get_results("SELECT * FROM $table ORDER BY ordering ASC");
        include __DIR__ . '/../views/admin-list.php';
    }

    public static function page_edit()
    {
        global $wpdb;
        $table_banners = Advanced_Banners_Manager::instance()->table_banners;
        $table_items = Advanced_Banners_Manager::instance()->table_items;

        $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $banner = $edit_id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_banners WHERE id=%d", $edit_id)) : null;
        
        $items = $edit_id ? $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_items WHERE banner_id=%d ORDER BY ordering ASC", $edit_id)) : [];

        include __DIR__ . '/../views/admin-edit.php';
    }
}
