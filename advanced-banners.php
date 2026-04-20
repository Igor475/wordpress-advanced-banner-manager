<?php

/**

 * Plugin Name: Advanced Banners Manager

 * Description: Gerenciamento avançado de banners com múltiplos itens, agendamento por data/hora, shortcodes e logs.

 * Version: 2.0.0

 * Author: Igor Matos

 * Text Domain: advanced-banners

 */



if (!defined('ABSPATH'))

    exit;



class Advanced_Banners_Manager

{

    private static $instance = null;

    public $version = '2.0.0';

    public $table_banners;

    public $table_items;

    public $table_logs;



    public static function instance()

    {

        if (self::$instance === null)

            self::$instance = new self();

        return self::$instance;

    }



    private function __construct()

    {

        global $wpdb;

        $this->table_banners = $wpdb->prefix . 'ab_banners';

        $this->table_items = $wpdb->prefix . 'ab_items';

        $this->table_logs = $wpdb->prefix . 'ab_logs';



        $this->includes();



        // Cria tabelas ao ativar o plugin

        register_activation_hook(__FILE__, ['AB_Activator', 'activate']);



        // Garante que as tabelas existam também ao carregar o plugin

        add_action('plugins_loaded', function () {

            if (class_exists('AB_Activator') && method_exists('AB_Activator', 'activate')) {

                AB_Activator::activate();

            }

        });



        add_action('admin_menu', ['AB_Admin', 'menu']);

        add_action('admin_enqueue_scripts', ['AB_Admin', 'assets']);

        add_action('wp_enqueue_scripts', [$this, 'front_assets']);



        // AJAX

        AB_Ajax::init();



        // Shortcode

        add_shortcode('banners', ['AB_Shortcode', 'render']);

    }

    private function includes()

    {

        require_once __DIR__ . '/includes/class-ab-activator.php';

        require_once __DIR__ . '/includes/class-ab-admin.php';

        require_once __DIR__ . '/includes/class-ab-ajax.php';

        require_once __DIR__ . '/includes/class-ab-shortcode.php';

        require_once __DIR__ . '/includes/class-ab-logger.php';

    }



    public function front_assets()

    {

        wp_enqueue_script(

            'ab-front-js',

            plugin_dir_url(__FILE__) . 'assets/js/front.js',

            ['jquery'],

            $this->version,

            true

        );



        wp_enqueue_style(

            'ab-front-css',

            plugin_dir_url(__FILE__) . 'assets/css/front.css',

            [],

            $this->version

        );



        // Mantém compatível com front.js que usa AB_Front_Ajax

        wp_localize_script('ab-front-js', 'AB_Front_Ajax', [

            'ajax_url' => admin_url('admin-ajax.php'),

            'nonce' => wp_create_nonce('ab_nonce') // usa o mesmo que seu check_ajax_referer

        ]);

    }





}



Advanced_Banners_Manager::instance();