<?php
if (!defined('ABSPATH')) {
    exit;
}

// Cria manualmente as tabelas do plugin.
function ab_create_tables()
{
    if (!class_exists('AB_Activator')) {
        require_once __DIR__ . '/class-ab-activator.php';
    }

    AB_Activator::activate();
}
