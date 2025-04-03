<?php
/*
Plugin Name: Wasi API | Integration Pro
Description: Integración con API Wasi para mostrar propiedades
Version: 3.1
Author: Leonardo Morales L.
*/

defined('ABSPATH') or exit;

// Carga las traducciones
add_action('plugins_loaded', function() {
    load_plugin_textdomain(
        'wasi-api',
        false,
        dirname(plugin_basename(__FILE__)).'/languages/'
    );
});

require_once __DIR__.'/includes/WasiAPI.php';
require_once __DIR__.'/includes/PropertyShortcode.php';
require_once __DIR__.'/includes/AdminSettings.php';

// Registro del widget de Elementor
add_action('elementor/widgets/register', function($widgets_manager) {
    require_once __DIR__ . '/includes/Elementor_Wasi_Widget.php';
    $widgets_manager->register(new Elementor_Wasi_Widget());
});

class Wasi_Main {
    public function __construct() {
        WasiAPI::init();
        new PropertyShortcode();
        new AdminSettings();
        
        add_action('wp_enqueue_scripts', [$this, 'load_assets']);
        add_action('admin_notices', [$this, 'show_admin_notices']);
    }
    
    public function load_assets() {
        // Estilos CSS
        wp_enqueue_style(
            'wasi-styles',
            plugins_url('assets/css/wasi.css', __FILE__),
            [], 
            filemtime(plugin_dir_path(__FILE__).'assets/css/wasi.css')
        );
        
        // Font Awesome
        wp_enqueue_style(
            'wasi-font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css'
        );
        
        // Scripts JS
        wp_enqueue_script(
            'wasi-frontend',
            plugins_url('assets/js/wasi-frontend.js', __FILE__),
            ['jquery'],
            filemtime(plugin_dir_path(__FILE__).'assets/js/wasi-frontend.js'),
            true
        );
        
        // Localizar script para AJAX
        wp_localize_script('wasi-frontend', 'wasi_vars', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wasi_ajax_nonce'),
            'loading_text' => __('Cargando...', 'wasi-api'),
            'load_more_text' => __('Cargar más', 'wasi-api'),
            'apply_filters_text' => __('Aplicar Filtros', 'wasi-api'),
            'error_text' => __('Ocurrió un error', 'wasi-api')
        ]);
        
        // Si es slider, cargar Slick
        if (is_singular() && has_shortcode(get_post()->post_content, 'wasi_properties')) {
            wp_enqueue_style(
                'slick-carousel',
                'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css'
            );
            wp_enqueue_script(
                'slick-carousel',
                'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
                ['jquery'],
                '1.8.1',
                true
            );
        }
    }
    
    public function show_admin_notices() {
        if(empty(get_option('wasi_api_token'))) {
            echo '<div class="notice notice-warning"><p>';
            echo '<strong>'.__('Wasi API:', 'wasi-api').'</strong> ';
            printf(
                __('Configure su API Token en %sConfiguración Wasi%s', 'wasi-api'),
                '<a href="'.admin_url('options-general.php?page=wasi-api').'">',
                '</a>'
            );
            echo '</p></div>';
        }
    }
}

new Wasi_Main();