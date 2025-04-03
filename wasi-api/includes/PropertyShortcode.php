<?php
class PropertyShortcode {
    public function __construct() {
        add_shortcode('wasi_properties', [$this, 'render_properties']);
        add_action('wp_ajax_wasi_load_more', [$this, 'ajax_load_more']);
        add_action('wp_ajax_nopriv_wasi_load_more', [$this, 'ajax_load_more']);
        add_action('wp_ajax_wasi_filter_properties', [$this, 'ajax_filter_properties']);
        add_action('wp_ajax_nopriv_wasi_filter_properties', [$this, 'ajax_filter_properties']);
    }

    public function render_properties($atts) {
        $atts = shortcode_atts([
            'limit' => 100,
            'page' => 1,
            'type' => '',
            'status' => 'all',
            'pagination' => 'no',
            'columns' => 3,
            'view_type' => 'grid',
            'show_filters' => 'no',
            'filters' => 'search,price_range,property_type',
            'details_button_text' => __('Ver detalles', 'wasi-api'),
            'loadmore_button_text' => __('Cargar más propiedades', 'wasi-api'),
            'filter_button_text' => __('Aplicar Filtros', 'wasi-api'),
            'debug' => false
        ], $atts);

        // Procesamiento de filtros
        if ($atts['show_filters'] === 'yes') {
            ob_start();
            $this->render_filters($atts['filters'], $atts['filter_button_text']);
            $filters_html = ob_get_clean();
        }

        // Construir args para API
        $args = $this->build_api_args($atts);

        // Obtener propiedades
        $properties = WasiAPI::get_properties($args);

        // Renderizar vista
        return $this->render_view($properties, $atts, $filters_html ?? '');
    }

    protected function render_filters($filters, $button_text) {
        $available_filters = explode(',', $filters);
        ?>
        <div class="wasi-filters">
            <?php if (in_array('search', $available_filters)): ?>
                <div class="wasi-filter search-filter" data-filter="search">
                    <input type="text" placeholder="<?php esc_attr_e('Buscar...', 'wasi-api'); ?>" class="wasi-search-input">
                </div>
            <?php endif; ?>
            
            <?php if (in_array('price_range', $available_filters)): ?>
                <div class="wasi-filter price-filter" data-filter="price">
                    <label><?php _e('Rango de Precio:', 'wasi-api'); ?></label>
                    <input type="number" class="wasi-price-min" placeholder="<?php esc_attr_e('Mín', 'wasi-api'); ?>">
                    <input type="number" class="wasi-price-max" placeholder="<?php esc_attr_e('Máx', 'wasi-api'); ?>">
                </div>
            <?php endif; ?>
            
            <button class="wasi-apply-filters"><?php echo esc_html($button_text); ?></button>
        </div>
        <?php
    }

    protected function build_api_args($atts) {
        $args = [
            
            'per_page' => absint($atts['limit']),
            'page' => absint($atts['page']),
            'status' => sanitize_text_field($atts['status'])
        ];

        if (!empty($atts['type'])) {
            $args['id_property_type'] = array_map('sanitize_text_field', explode(',', $atts['type']));
        }

        return $args;
    }

    protected function render_view($properties, $atts, $filters_html) {
        // Añade clase CSS para columnas
        add_filter('body_class', function($classes) use ($atts) {
            $classes[] = 'wasi-columns-' . absint($atts['columns']);
            return $classes;
        });

        ob_start();
        ?>
        <style>
            .wasi-properties-grid {
                display: grid;
                gap: 25px;
                margin: 30px 0;
            }
            .wasi-columns-2 .wasi-properties-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .wasi-columns-3 .wasi-properties-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .wasi-columns-4 .wasi-properties-grid {
                grid-template-columns: repeat(4, 1fr);
            }
            @media (max-width: 1024px) {
                .wasi-properties-grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }
            @media (max-width: 600px) {
                .wasi-properties-grid {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>

        <div class="wasi-properties-container view-<?php echo esc_attr($atts['view_type']); ?>" data-page="1" data-args="<?php echo esc_attr(json_encode($atts)); ?>">
            <?php echo $filters_html; ?>
            
            <?php if (is_wp_error($properties)): ?>
                <div class="wasi-error"><?php echo $properties->get_error_message(); ?></div>
            <?php elseif (empty($properties)): ?>
                <div class="wasi-notice"><?php _e('No se encontraron propiedades.', 'wasi-api'); ?></div>
            <?php else: ?>
                <?php if ($atts['view_type'] === 'slider'): ?>
                    <div class="wasi-properties-slider">
                        <?php foreach ($properties as $property): ?>
                            <div class="wasi-slide"><?php $this->render_property_card($property, $atts['view_type'], $atts['details_button_text']); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="wasi-properties-<?php echo esc_attr($atts['view_type']); ?>">
                        <?php foreach ($properties as $property): ?>
                            <?php $this->render_property_card($property, $atts['view_type'], $atts['details_button_text']); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($atts['pagination'] === 'yes'): ?>
                    <div class="wasi-pagination">
                        <button class="wasi-load-more"><?php echo esc_html($atts['loadmore_button_text']); ?></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    protected function render_property_card($property, $view_type = 'grid', $button_text = '') {
        $button_text = empty($button_text) ? __('Ver detalles', 'wasi-api') : $button_text;
        ?>
        <div class="wasi-property-card <?php echo esc_attr($view_type); ?>">
            <div class="property-image">
                <?php if (!empty($property['main_image']['url'])): ?>
                    <img src="<?php echo esc_url($property['main_image']['url']); ?>" 
                         alt="<?php echo esc_attr($property['title'] ?? __('Propiedad', 'wasi-api')); ?>"
                         loading="lazy">
                <?php else: ?>
                    <img src="<?php echo plugins_url('assets/img/placeholder.jpg', __FILE__); ?>" 
                         alt="<?php esc_attr_e('Imagen no disponible', 'wasi-api'); ?>"
                         loading="lazy">
                <?php endif; ?>
                
                <?php if (!empty($property['price'])): ?>
                    <div class="property-badge price">
                        <?php echo esc_html($this->format_price($property['price'])); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="property-content">
                <h3 class="property-title"><?php echo esc_html($property['title'] ?? __('Propiedad', 'wasi-api')); ?></h3>
                
                <div class="property-meta">
                    <?php if (isset($property['sale_price']) && !empty($property['sale_price'])): ?>
                        <p class="price sale-price">
                            <i class="fas fa-tag"></i> 
                            <?php _e('Venta:', 'wasi-api'); ?> 
                            <strong><?php echo $this->format_price($property['sale_price']); ?></strong>
                        </p>
                    <?php endif; ?>

                    <?php if (isset($property['rent_price']) && !empty($property['rent_price'])): ?>
                        <p class="price rent-price">
                            <i class="fas fa-money-bill-wave"></i> 
                            <?php _e('Renta:', 'wasi-api'); ?> 
                            <strong><?php echo $this->format_price($property['rent_price']); ?>/mes</strong>
                        </p>
                    <?php endif; ?>
                
                    <?php if (!empty($property['location'])): ?>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html($property['location']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="property-features">
                        <?php if (!empty($property['bedrooms'])): ?>
                            <span class="feature bedrooms">
                                <i class="fas fa-bed"></i>
                                <?php echo absint($property['bedrooms']); ?> <?php _e('hab.', 'wasi-api'); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['bathrooms'])): ?>
                            <span class="feature bathrooms">
                                <i class="fas fa-bath"></i>
                                <?php echo absint($property['bathrooms']); ?> <?php _e('baños', 'wasi-api'); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['area'])): ?>
                            <span class="feature area">
                                <i class="fas fa-ruler-combined"></i>
                                <?php echo absint($property['area']); ?> m²
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <a href="<?php echo esc_url($property['url'] ?? '#'); ?>" class="wasi-details-button">
                    <?php echo esc_html($button_text); ?>
                </a>
            </div>
        </div>
        <?php
    }

    protected function format_price($price) {
        return '$' . number_format(floatval($price), 0, ',', '.');
    }

    public function ajax_load_more() {
        check_ajax_referer('wasi_ajax_nonce', 'security');

        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $args = isset($_POST['args']) ? $_POST['args'] : [];
        
        $args['page'] = $page;
        $properties = WasiAPI::get_properties($args);

        if (is_wp_error($properties) || empty($properties)) {
            wp_send_json_error(['message' => __('No hay más propiedades', 'wasi-api')]);
        }

        ob_start();
        foreach ($properties as $property) {
            $this->render_property_card($property, $args['view_type'] ?? 'grid', $args['details_button_text'] ?? '');
        }
        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'is_last_page' => count($properties) < ($args['limit'] ?? 6)
        ]);
    }

    public function ajax_filter_properties() {
        check_ajax_referer('wasi_ajax_nonce', 'security');

        $filters = isset($_POST['filters']) ? $_POST['filters'] : [];
        $args = isset($_POST['args']) ? $_POST['args'] : [];
        
        // Aplicar filtros
        if (!empty($filters['search'])) {
            $args['search'] = sanitize_text_field($filters['search']);
        }
        
        if (!empty($filters['price'])) {
            $args['min_price'] = floatval($filters['price']['min']);
            $args['max_price'] = floatval($filters['price']['max']);
        }

        $properties = WasiAPI::get_properties($args);

        if (is_wp_error($properties)) {
            wp_send_json_error(['message' => $properties->get_error_message()]);
        }

        ob_start();
        foreach ($properties as $property) {
            $this->render_property_card($property, $args['view_type'] ?? 'grid', $args['details_button_text'] ?? '');
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }
}