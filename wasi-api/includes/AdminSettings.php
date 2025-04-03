<?php
class AdminSettings {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu() {
        add_options_page(
            __('Configuración Wasi API', 'wasi-api'),
            __('Wasi API', 'wasi-api'),
            'manage_options',
            'wasi-api',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('wasi_settings', 'wasi_api_token');
        register_setting('wasi_settings', 'wasi_client_id');
        
        add_settings_section(
            'wasi_api_section',
            __('Credenciales API', 'wasi-api'),
            [$this, 'section_callback'],
            'wasi-api'
        );
        
        add_settings_field(
            'wasi_client_id',
            __('Client ID', 'wasi-api'),
            [$this, 'client_id_callback'],
            'wasi-api',
            'wasi_api_section'
        );
        
        add_settings_field(
            'wasi_api_token',
            __('API Token', 'wasi-api'),
            [$this, 'api_token_callback'],
            'wasi-api',
            'wasi_api_section'
        );
    }

    public function section_callback() {
        echo '<p>'.__('Ingresa las credenciales de tu API Wasi', 'wasi-api').'</p>';
    }

    public function client_id_callback() {
        $value = get_option('wasi_client_id', '1300521');
        echo '<input type="text" name="wasi_client_id" value="'.esc_attr($value).'" class="regular-text">';
    }

    public function api_token_callback() {
        $value = get_option('wasi_api_token');
        echo '<input type="password" name="wasi_api_token" value="'.esc_attr($value).'" class="regular-text">';
        echo '<p class="description">'.__('Este token se puede obtener desde el panel de control de Wasi', 'wasi-api').'</p>';
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Configuración Wasi API', 'wasi-api'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wasi_settings');
                do_settings_sections('wasi-api');
                submit_button(__('Guardar cambios', 'wasi-api'));
                ?>
            </form>
        </div>
        <?php
    }
}