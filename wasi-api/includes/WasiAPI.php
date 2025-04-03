<?php
class WasiAPI {
    private static $client_id = '1300521';
    private static $api_token = '';
    private static $api_url = 'https://api.wasi.co/v1';
    private static $max_per_page = 100;
    private static $max_pages = 20;
    private static $post_type = 'wasi_property';

    public static function init() {
        // 1. Configuración inicial del token
        if (defined('WASI_API_TOKEN')) {
            self::$api_token = WASI_API_TOKEN;
        } else {
            self::$api_token = get_option('wasi_api_token', '');
        }
        
        // 2. Mostrar error si no hay token
        if (empty(self::$api_token)) {
            error_log('Wasi API Error: Token no configurado');
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error">';
                echo '<strong>Error Wasi API:</strong> El token API no está configurado. ';
                echo 'Por favor define WASI_API_TOKEN en wp-config.php o ';
                echo '<a href="'.esc_url(admin_url('options-general.php?page=wasi-api')).'">configúralo aquí</a>.';
                echo '</div>';
            });
            return false;
        }
        
        // 3. Registrar menú y CPT
        add_action('admin_menu', [self::class, 'register_admin_menu']);
        add_action('init', [self::class, 'register_cpt']);
        add_action('add_meta_boxes', [self::class, 'add_meta_boxes']);
        add_action('save_post_'.self::$post_type, [self::class, 'save_property_meta'], 10, 2);
        
        // 4. Programar sincronización
        if (!wp_next_scheduled('wasi_daily_sync')) {
            wp_schedule_event(time(), 'daily', 'wasi_daily_sync');
        }
        add_action('wasi_daily_sync', [self::class, 'sync_all_properties']);
        
        // 5. Registrar widgets de Elementor
        add_action('elementor/widgets/register', [self::class, 'register_elementor_widgets']);
        add_action('elementor/elements/categories_registered', [self::class, 'add_elementor_category']);
        
        return true;
    }

    public static function register_cpt() {
        $labels = [
            'name' => __('Propiedades Wasi', 'wasi-api'),
            'singular_name' => __('Propiedad Wasi', 'wasi-api'),
            'menu_name' => __('Propiedades Wasi', 'wasi-api'),
            'add_new' => __('Agregar Nueva', 'wasi-api'),
            'add_new_item' => __('Agregar Nueva Propiedad', 'wasi-api'),
            'edit_item' => __('Editar Propiedad', 'wasi-api'),
            'new_item' => __('Nueva Propiedad', 'wasi-api'),
            'view_item' => __('Ver Propiedad', 'wasi-api'),
            'search_items' => __('Buscar Propiedades', 'wasi-api'),
            'not_found' => __('No se encontraron propiedades', 'wasi-api'),
            'not_found_in_trash' => __('No hay propiedades en la papelera', 'wasi-api')
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields', 'elementor'],
            'menu_icon' => 'dashicons-building',
            'rewrite' => ['slug' => 'propiedades'],
            'capability_type' => 'post',
            'show_ui' => true,
            'has_archive' => true,
            'map_meta_cap' => true,
        ];

        register_post_type(self::$post_type, $args);
    }

    public static function register_admin_menu() {
        add_menu_page(
            __('Propiedades Wasi', 'wasi-api'),
            __('Wasi Propiedades', 'wasi-api'),
            'manage_options',
            'wasi-properties',
            [self::class, 'render_admin_page'],
            'dashicons-building',
            6
        );
    }

    public static function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para acceder a esta página.', 'wasi-api'));
        }

        echo '<div class="wrap">';
        echo '<h1><span class="dashicons dashicons-building"></span> ' . esc_html__('Administrar Propiedades Wasi', 'wasi-api') . '</h1>';
        
        if (isset($_GET['sync']) && $_GET['sync'] == 1) {
            check_admin_referer('wasi_sync');
            $result = self::sync_all_properties();
            if ($result) {
                echo '<div class="notice notice-success is-dismissible"><p>'
                    .esc_html__('Sincronización completada.', 'wasi-api').'</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>'
                    .esc_html__('Error durante la sincronización. Revisa los logs.', 'wasi-api').'</p></div>';
            }
        }

        echo '<div class="wasi-admin-actions" style="margin: 20px 0;">';
        echo '<a href="'.esc_url(wp_nonce_url(admin_url('admin.php?page=wasi-properties&sync=1'), 'wasi_sync')).'" class="button button-primary">';
        echo '<span class="dashicons dashicons-update"></span> '.esc_html__('Sincronizar Propiedades', 'wasi-api');
        echo '</a>';
        echo '</div>';

        $properties_query = new WP_Query([
            'post_type' => self::$post_type,
            'posts_per_page' => 20,
            'meta_key' => 'wasi_id',
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        ]);

        if ($properties_query->have_posts()) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>
                    <th>'.esc_html__('ID', 'wasi-api').'</th>
                    <th>'.esc_html__('Imagen', 'wasi-api').'</th>
                    <th>'.esc_html__('Título', 'wasi-api').'</th>
                    <th>'.esc_html__('Precio', 'wasi-api').'</th>
                    <th>'.esc_html__('Ubicación', 'wasi-api').'</th>
                    <th>'.esc_html__('Acciones', 'wasi-api').'</th>
                  </tr></thead><tbody>';

            while ($properties_query->have_posts()) {
                $properties_query->the_post();
                $wasi_id = get_post_meta(get_the_ID(), 'wasi_id', true);
                $price = get_post_meta(get_the_ID(), 'price', true);
                $location = get_post_meta(get_the_ID(), 'location', true);
                
                echo '<tr>
                        <td>'.esc_html($wasi_id).'</td>
                        <td>'.get_the_post_thumbnail(null, [50, 50], ['style' => 'object-fit: cover;']).'</td>
                        <td><strong>'.esc_html(get_the_title()).'</strong></td>
                        <td>'.esc_html($price).'</td>
                        <td>'.esc_html($location).'</td>
                        <td>
                            <a href="'.esc_url(get_edit_post_link()).'" class="button">'.esc_html__('Editar', 'wasi-api').'</a>
                            <a href="'.esc_url(get_permalink()).'" class="button" target="_blank">'.esc_html__('Ver', 'wasi-api').'</a>
                        </td>
                      </tr>';
            }

            echo '</tbody></table>';
            wp_reset_postdata();
        } else {
            echo '<div class="notice notice-warning"><p>'
                .esc_html__('No hay propiedades. Haz clic en "Sincronizar Propiedades" para importarlas.', 'wasi-api')
                .'</p></div>';
        }

        echo '</div>';
    }

    public static function add_meta_boxes() {
        add_meta_box(
            'wasi_editable_fields',
            __('Datos Editables', 'wasi-api'),
            [self::class, 'render_editable_fields'],
            self::$post_type,
            'normal',
            'high'
        );

        add_meta_box(
            'wasi_api_data',
            __('Datos de Wasi API', 'wasi-api'),
            [self::class, 'render_api_data'],
            self::$post_type,
            'side',
            'default'
        );
    }

    public static function render_editable_fields($post) {
        wp_nonce_field('wasi_save_meta', 'wasi_meta_nonce');
        
        $fields = [
            'descripcion_seo' => [
                'label' => __('Descripción SEO', 'wasi-api'),
                'type' => 'textarea',
                'rows' => 5
            ],
            'features' => [
                'label' => __('Características (una por línea)', 'wasi-api'),
                'type' => 'textarea',
                'rows' => 10
            ],
            'notas_internas' => [
                'label' => __('Notas Internas', 'wasi-api'),
                'type' => 'textarea',
                'rows' => 5
            ]
        ];
        
        foreach ($fields as $meta_key => $field) {
            $value = get_post_meta($post->ID, $meta_key, true);
            echo '<div style="margin-bottom: 15px;">';
            echo '<label for="'.esc_attr($meta_key).'"><strong>'.esc_html($field['label']).':</strong></label>';
            
            if ($field['type'] === 'textarea') {
                echo '<textarea id="'.esc_attr($meta_key).'" name="'.esc_attr($meta_key).'" 
                      style="width: 100%;" rows="'.esc_attr($field['rows']).'">'.esc_textarea($value).'</textarea>';
            } else {
                echo '<input type="'.esc_attr($field['type']).'" id="'.esc_attr($meta_key).'" 
                      name="'.esc_attr($meta_key).'" value="'.esc_attr($value).'" class="widefat">';
            }
            
            echo '</div>';
        }
    }

    public static function render_api_data($post) {
        $metadata = get_post_meta($post->ID);
        
        $fields = [
            'ID Wasi' => 'wasi_id',
            'Precio' => 'price',
            'Ubicación' => 'location',
            'Tipo' => 'type',
            'Provincia' => 'provincia',
            'Ciudad' => 'ciudad',
            'Localidad' => 'localidad',
            'Zona' => 'zona',
            'Tipo de Inmueble' => 'tipo_inmueble',
            'Estado Propiedad' => 'estado_propiedad',
            'Tipo de Negocio' => 'tipo_negocio',
            'Habitaciones' => 'habitaciones',
            'Baños' => 'banos',
            'Área (m²)' => 'area'
        ];
        
        echo '<ul>';
        foreach ($fields as $label => $meta_key) {
            $value = $metadata[$meta_key][0] ?? '';
            if (!empty($value)) {
                echo '<li><strong>'.esc_html__($label, 'wasi-api').':</strong> '.esc_html($value).'</li>';
            }
        }
        echo '</ul>';
        
        $descripcion = $metadata['descripcion_larga'][0] ?? '';
        if (!empty($descripcion)) {
            echo '<div class="wasi-description"><strong>'.esc_html__('Descripción:', 'wasi-api').'</strong>';
            echo '<div>'.wp_kses_post($descripcion).'</div></div>';
        }
        
        $url = $metadata['url_wasi'][0] ?? '';
        if (!empty($url)) {
            echo '<div style="margin-top: 10px;"><a href="'.esc_url($url).'" target="_blank" class="button">';
            echo esc_html__('Ver en Wasi.co', 'wasi-api');
            echo '</a></div>';
        }
        
        echo '<p><em>'.esc_html__('Estos datos se sincronizan automáticamente con Wasi y no se pueden editar aquí.').'</em></p>';
    }

    public static function save_property_meta($post_id, $post) {
        if (!isset($_POST['wasi_meta_nonce']) || 
            !wp_verify_nonce($_POST['wasi_meta_nonce'], 'wasi_save_meta') ||
            !current_user_can('edit_post', $post_id) ||
            wp_is_post_autosave($post_id) ||
            wp_is_post_revision($post_id)) {
            return;
        }

        $editable_fields = ['descripcion_seo', 'features', 'notas_internas'];
        
        foreach ($editable_fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'features') {
                    $value = array_filter(array_map('trim', explode("\n", sanitize_textarea_field($_POST[$field]))));
                } else {
                    $value = sanitize_textarea_field($_POST[$field]);
                }
                update_post_meta($post_id, $field, $value);
            }
        }
    }

    public static function sync_all_properties() {
        if (empty(self::$api_token)) {
            self::init();
            if (empty(self::$api_token)) {
                return false;
            }
        }

        $properties = self::get_properties(['per_page' => self::$max_per_page, 'page' => 1]);
        
        if (is_wp_error($properties)) {
            error_log('Error en sincronización Wasi: '.$properties->get_error_message());
            return false;
        }

        $count = 0;
        foreach ($properties as $property) {
            if (self::sync_single_property($property)) {
                $count++;
            }
        }
        
        error_log("Wasi API: Sincronizadas $count propiedades");
        return $count > 0;
    }

    public static function sync_single_property($property_data) {
        if (empty($property_data['id_property'])) {
            error_log('Wasi API Error: Datos de propiedad incompletos');
            return false;
        }

        $existing = get_posts([
            'post_type' => self::$post_type,
            'meta_key' => 'wasi_id',
            'meta_value' => $property_data['id_property'],
            'posts_per_page' => 1,
            'post_status' => 'any'
        ]);

        $post_data = [
            'post_title' => sanitize_text_field($property_data['title'] ?? 'Propiedad sin título'),
            'post_type' => self::$post_type,
            'post_status' => 'publish',
            'meta_input' => [
                'wasi_id' => sanitize_text_field($property_data['id_property']),
                'price' => sanitize_text_field($property_data['price'] ?? ''),
                'location' => sanitize_text_field($property_data['location'] ?? ''),
                'type' => sanitize_text_field($property_data['type'] ?? ''),
                'provincia' => sanitize_text_field($property_data['province'] ?? ''),
                'ciudad' => sanitize_text_field($property_data['city'] ?? ''),
                'localidad' => sanitize_text_field($property_data['locality'] ?? ''),
                'zona' => sanitize_text_field($property_data['zone'] ?? ''),
                'tipo_inmueble' => sanitize_text_field($property_data['property_type'] ?? ''),
                'estado_propiedad' => sanitize_text_field($property_data['property_status'] ?? ''),
                'tipo_negocio' => sanitize_text_field($property_data['business_type'] ?? ''),
                'habitaciones' => absint($property_data['bedrooms'] ?? 0),
                'banos' => absint($property_data['bathrooms'] ?? 0),
                'area' => absint($property_data['area'] ?? 0),
                'descripcion_larga' => wp_kses_post($property_data['description'] ?? ''),
                'url_wasi' => esc_url_raw($property_data['url'] ?? '')
            ]
        ];

        try {
            if (!empty($existing)) {
                $post_data['ID'] = $existing[0]->ID;
                $post_id = wp_update_post($post_data, true);
            } else {
                $post_id = wp_insert_post($post_data, true);
            }

            if (is_wp_error($post_id)) {
                throw new Exception($post_id->get_error_message());
            }

            // Sincronizar imagen destacada
            if (!empty($property_data['main_image']['url'])) {
                $image_id = self::upload_remote_image($property_data['main_image']['url'], $post_id);
                if ($image_id && !is_wp_error($image_id)) {
                    set_post_thumbnail($post_id, $image_id);
                }
            }

            return $post_id;
        } catch (Exception $e) {
            error_log('Error al sincronizar propiedad Wasi ID '.$property_data['id_property'].': '.$e->getMessage());
            return false;
        }
    }

    private static function upload_remote_image($image_url, $post_id) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            error_log('Wasi API Error: URL de imagen no válida: '.$image_url);
            return false;
        }

        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            error_log('Error al descargar imagen Wasi: '.$tmp->get_error_message());
            return false;
        }

        $file_array = [
            'name' => sanitize_file_name(basename($image_url)),
            'tmp_name' => $tmp
        ];

        $image_id = media_handle_sideload($file_array, $post_id);
        
        @unlink($tmp);

        if (is_wp_error($image_id)) {
            error_log('Error al subir imagen Wasi: '.$image_id->get_error_message());
            return false;
        }

        return $image_id;
    }

    public static function get_properties($args = []) {
        if (empty(self::$api_token)) {
            self::init();
            if (empty(self::$api_token)) {
                return new WP_Error('no_token', 'Token API no configurado');
            }
        }

        $cache_key = 'wasi_properties_' . md5(serialize($args));
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }

        $defaults = [
            'id_company' => self::$client_id,
            'wasi_token' => self::$api_token,
            'status' => 'all',
            'per_page' => 50,
            'page' => 1
        ];
        
        $params = array_merge($defaults, $args);
        $url = self::$api_url.'/property/search?'.http_build_query($params);
        
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'sslverify' => apply_filters('wasi_api_sslverify', true)
        ]);
        
        if (is_wp_error($response)) {
            error_log('Error conexión Wasi API: '.$response->get_error_message());
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($response_code !== 200) {
            $error_message = 'Wasi API HTTP Error '.$response_code;
            if (isset($body['message'])) {
                $error_message .= ': '.$body['message'];
            }
            error_log($error_message);
            return new WP_Error('api_error', $error_message, ['status' => $response_code]);
        }
        
        $properties = [];
        if (isset($body['properties'])) {
            $properties = $body['properties'];
        } elseif (isset($body[0]) && is_array($body[0])) {
            $properties = $body;
        } else {
            error_log('Wasi API: Formato de respuesta no reconocido');
            return new WP_Error('invalid_format', 'Formato de respuesta no reconocido');
        }

        set_transient($cache_key, $properties, HOUR_IN_SECONDS);
        
        return $properties;
    }

    public static function get_total_properties($args = []) {
        $args['per_page'] = 1;
        $response = self::get_properties($args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $headers = wp_remote_retrieve_headers($response);
        if (isset($headers['x-total-count'])) {
            return (int)$headers['x-total-count'];
        }
        
        return count($response);
    }

    public static function clear_cache() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wasi_properties_%'"
        );
        return true;
    }

    public static function register_elementor_widgets($widgets_manager) {
        require_once __DIR__.'/includes/Elementor/WasiPropertyWidget.php';
        $widgets_manager->register(new WasiPropertyWidget());
    }

    public static function add_elementor_category($elements_manager) {
        $elements_manager->add_category('real-estate', [
            'title' => __('Bienes Raíces', 'wasi-api'),
            'icon' => 'fa fa-building',
        ]);
    }
}