<?php
if (!defined('ABSPATH')) exit;

class Elementor_Wasi_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'wasi_real_estate_pro';
    }

    public function get_title() {
        return __('Wasi Properties PRO', 'wasi-api');
    }

    public function get_icon() {
        return 'eicon-gallery-justified';
    }

    public function get_categories() {
        return ['real-estate'];
    }

    public function get_keywords() {
        return ['wasi', 'properties', 'real estate', 'inmobiliaria'];
    }

    protected function register_controls() {
        // ========== SECCIÓN DE CONTENIDO ==========
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Configuración', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => __('Número de Propiedades', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
                'min' => 1,
                'max' => 500,
                'step' => 1
            ]
        );

        $this->add_control(
            'property_types',
            [
                'label' => __('Tipos de Propiedad', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_property_types(),
                'multiple' => true,
                'label_block' => true
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Ordenar por', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'price_asc' => __('Precio: Menor a Mayor', 'wasi-api'),
                    'price_desc' => __('Precio: Mayor a Menor', 'wasi-api'),
                    'date_desc' => __('Más Recientes', 'wasi-api'),
                    'date_asc' => __('Más Antiguas', 'wasi-api')
                ],
                'default' => 'date_desc'
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label' => __('Mostrar Paginación', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'wasi-api'),
                'label_off' => __('No', 'wasi-api'),
                'return_value' => 'yes',
                'default' => 'no'
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE DISEÑO ==========
        $this->start_controls_section(
            'layout_section',
            [
                'label' => __('Diseño', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'view_type',
            [
                'label' => __('Tipo de Vista', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'grid' => __('Grid', 'wasi-api'),
                    'list' => __('Lista', 'wasi-api'),
                    'slider' => __('Slider', 'wasi-api')
                ],
                'default' => 'grid',
                'prefix_class' => 'wasi-view-'
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columnas', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    6 => 6
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-properties-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
                'condition' => [
                    'view_type' => 'grid'
                ]
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE FILTROS ==========
        $this->start_controls_section(
            'filters_section',
            [
                'label' => __('Filtros', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => __('Mostrar Filtros', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'wasi-api'),
                'label_off' => __('No', 'wasi-api'),
                'return_value' => 'yes',
                'default' => 'no'
            ]
        );

        $this->add_control(
            'available_filters',
            [
                'label' => __('Filtros Disponibles', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                    'search' => __('Búsqueda', 'wasi-api'),
                    'price_range' => __('Rango de Precios', 'wasi-api'),
                    'property_type' => __('Tipo de Propiedad', 'wasi-api'),
                    'location' => __('Ubicación', 'wasi-api'),
                    'bedrooms' => __('Habitaciones', 'wasi-api'),
                    'bathrooms' => __('Baños', 'wasi-api')
                ],
                'multiple' => true,
                'label_block' => true,
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - BOTONES ==========
        $this->start_controls_section(
            'buttons_style_section',
            [
                'label' => __('Estilo de Botones', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // ----- Botón "Ver detalles" -----
        $this->add_control(
            'details_button_heading',
            [
                'label' => __('Botón "Ver detalles"', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'details_button_text',
            [
                'label' => __('Texto del Botón', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Ver detalles', 'wasi-api'),
                'placeholder' => __('Ver detalles', 'wasi-api'),
            ]
        );

        $this->start_controls_tabs('details_button_tabs');

        $this->start_controls_tab(
            'details_button_normal',
            [
                'label' => __('Normal', 'wasi-api'),
            ]
        );

        $this->add_control(
            'details_button_color',
            [
                'label' => __('Color del Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'details_button_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'details_button_border',
                'selector' => '{{WRAPPER}} .wasi-property-card .wasi-details-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'details_button_hover',
            [
                'label' => __('Hover', 'wasi-api'),
            ]
        );

        $this->add_control(
            'details_button_hover_color',
            [
                'label' => __('Color del Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'details_button_hover_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'details_button_hover_border_color',
            [
                'label' => __('Color del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'details_button_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'details_button_typography',
                'selector' => '{{WRAPPER}} .wasi-property-card .wasi-details-button',
            ]
        );

        $this->add_responsive_control(
            'details_button_padding',
            [
                'label' => __('Padding', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'details_button_margin',
            [
                'label' => __('Margin', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .wasi-details-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // ----- Botón "Cargar más" -----
        $this->add_control(
            'loadmore_button_heading',
            [
                'label' => __('Botón "Cargar más"', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'loadmore_button_text',
            [
                'label' => __('Texto del Botón', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Cargar más propiedades', 'wasi-api'),
                'placeholder' => __('Cargar más propiedades', 'wasi-api'),
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->start_controls_tabs('loadmore_button_tabs');

        $this->start_controls_tab(
            'loadmore_button_normal',
            [
                'label' => __('Normal', 'wasi-api'),
            ]
        );

        $this->add_control(
            'loadmore_button_color',
            [
                'label' => __('Color del Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'loadmore_button_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'loadmore_button_border',
                'selector' => '{{WRAPPER}} .wasi-load-more',
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'loadmore_button_hover',
            [
                'label' => __('Hover', 'wasi-api'),
            ]
        );

        $this->add_control(
            'loadmore_button_hover_color',
            [
                'label' => __('Color del Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'loadmore_button_hover_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'loadmore_button_hover_border_color',
            [
                'label' => __('Color del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'loadmore_button_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'loadmore_button_typography',
                'selector' => '{{WRAPPER}} .wasi-load-more',
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'loadmore_button_padding',
            [
                'label' => __('Padding', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'loadmore_button_margin',
            [
                'label' => __('Margin', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'pagination' => 'yes'
                ]
            ]
        );

        // ----- Botón "Aplicar Filtros" -----
        $this->add_control(
            'filter_button_heading',
            [
                'label' => __('Botón "Aplicar Filtros"', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filter_button_text',
            [
                'label' => __('Texto del Botón', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Aplicar Filtros', 'wasi-api'),
                'placeholder' => __('Aplicar Filtros', 'wasi-api'),
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->start_controls_tabs('filter_button_tabs');

        $this->start_controls_tab(
            'filter_button_normal',
            [
                'label' => __('Normal', 'wasi-api'),
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filter_button_color',
            [
                'label' => __('Color del Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filter_button_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'filter_button_border',
                'selector' => '{{WRAPPER}} .wasi-filters .wasi-apply-filters',
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'filter_button_hover',
            [
                'label' => __('Hover', 'wasi-api'),
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filter_button_hover_color',
            [
                'label' => __('Color del Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filter_button_hover_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filter_button_hover_border_color',
            [
                'label' => __('Color del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'filter_button_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_button_typography',
                'selector' => '{{WRAPPER}} .wasi-filters .wasi-apply-filters',
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'filter_button_padding',
            [
                'label' => __('Padding', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'filter_button_margin',
            [
                'label' => __('Margin', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters .wasi-apply-filters' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - TARJETAS ==========
        $this->start_controls_section(
            'card_style_section',
            [
                'label' => __('Estilo de Tarjetas', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => __('Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .wasi-property-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .wasi-property-card',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Espaciado Interno', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_margin',
            [
                'label' => __('Espaciado Externo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_animation',
            [
                'label' => __('Animación al Pasar Mouse', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
                'prefix_class' => 'elementor-animation-',
            ]
        );

        $this->add_control(
            'card_hover_effect',
            [
                'label' => __('Efecto Hover', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'none' => __('Ninguno', 'wasi-api'),
                    'shadow' => __('Sombra', 'wasi-api'),
                    'scale' => __('Escalar', 'wasi-api'),
                    'fade' => __('Desvanecer', 'wasi-api')
                ],
                'default' => 'shadow',
                'prefix_class' => 'wasi-card-hover-'
            ]
        );

        $this->start_controls_tabs('card_hover_tabs');

        $this->start_controls_tab(
            'card_hover_normal',
            [
                'label' => __('Normal', 'wasi-api'),
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'card_hover_hover',
            [
                'label' => __('Hover', 'wasi-api'),
            ]
        );

        $this->add_control(
            'card_bg_color_hover',
            [
                'label' => __('Color de Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_hover_shadow',
            [
                'label' => __('Sombra', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::BOX_SHADOW,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card:hover' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
                ],
                'condition' => [
                    'card_hover_effect' => 'shadow'
                ]
            ]
        );

        $this->add_control(
            'card_hover_scale',
            [
                'label' => __('Escala', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1.2,
                        'step' => 0.01
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card:hover' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'card_hover_effect' => 'scale'
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - IMÁGENES ==========
        $this->start_controls_section(
            'image_style_section',
            [
                'label' => __('Estilo de Imágenes', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label' => __('Altura', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                        'step' => 5
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-image, {{WRAPPER}} .wasi-property-card .property-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters',
                'selector' => '{{WRAPPER}} .wasi-property-card .property-image img',
            ]
        );

        $this->start_controls_tabs('image_hover_tabs');

        $this->start_controls_tab(
            'image_hover_normal',
            [
                'label' => __('Normal', 'wasi-api'),
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => __('Opacidad', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0,
                        'step' => 0.01
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'image_hover_hover',
            [
                'label' => __('Hover', 'wasi-api'),
            ]
        );

        $this->add_control(
            'image_opacity_hover',
            [
                'label' => __('Opacidad', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0,
                        'step' => 0.01
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card:hover .property-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'image_scale_hover',
            [
                'label' => __('Escala', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1.2,
                        'step' => 0.01
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card:hover .property-image img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters_hover',
                'selector' => '{{WRAPPER}} .wasi-property-card:hover .property-image img',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - TÍTULOS ==========
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Estilo de Títulos', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Color Hover', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card:hover .property-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .wasi-property-card .property-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margen', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __('Padding', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title_alignment',
            [
                'label' => __('Alineación', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Izquierda', 'wasi-api'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Centro', 'wasi-api'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Derecha', 'wasi-api'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - PRECIOS ==========
        $this->start_controls_section(
            'price_style_section',
            [
                'label' => __('Estilo de Precios', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'price_background',
            [
                'label' => __('Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-price' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .wasi-property-card .property-price',
            ]
        );

        $this->add_responsive_control(
            'price_margin',
            [
                'label' => __('Margen', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_padding',
            [
                'label' => __('Padding', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'price_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - ICONOS ==========
        $this->start_controls_section(
            'icons_style_section',
            [
                'label' => __('Estilo de Iconos', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Color', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-features i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __('Tamaño', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 30,
                        'step' => 1
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 2,
                        'step' => 0.1
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-features i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_spacing',
            [
                'label' => __('Espaciado', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .wasi-property-card .property-features i' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========== SECCIÓN DE ESTILOS - FILTROS ==========
        $this->start_controls_section(
            'filters_style_section',
            [
                'label' => __('Estilo de Filtros', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_filters' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'filters_background',
            [
                'label' => __('Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'filters_border',
                'selector' => '{{WRAPPER}} .wasi-filters',
            ]
        );

        $this->add_control(
            'filters_border_radius',
            [
                'label' => __('Radio del Borde', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'filters_padding',
            [
                'label' => __('Padding', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'filters_margin',
            [
                'label' => __('Margen', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'filter_input_style',
            [
                'label' => __('Estilo de Inputs', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'filter_input_background',
            [
                'label' => __('Fondo', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_input_color',
            [
                'label' => __('Color de Texto', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wasi-filters input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_input_typography',
                'selector' => '{{WRAPPER}} .wasi-filters input',
            ]
        );

        $this->end_controls_section();
    }

    protected function get_property_types() {
        return [
            '1' => __('Casa', 'wasi-api'),
            '2' => __('Apartamento', 'wasi-api'),
            '3' => __('Terreno', 'wasi-api'),
            '4' => __('Local Comercial', 'wasi-api'),
            '5' => __('Oficina', 'wasi-api')
        ];
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Pasar los textos de los botones al shortcode
        $shortcode_atts = [
            'limit' => $settings['limit'],
            'type' => !empty($settings['property_types']) ? implode(',', $settings['property_types']) : '',
            'order' => $settings['order_by'],
            'pagination' => $settings['pagination'],
            'view_type' => $settings['view_type'],
            'show_filters' => $settings['show_filters'],
            'filters' => !empty($settings['available_filters']) ? implode(',', $settings['available_filters']) : '',
            'details_button_text' => $settings['details_button_text'],
            'loadmore_button_text' => $settings['loadmore_button_text'],
            'filter_button_text' => $settings['filter_button_text']
        ];

        echo do_shortcode('[wasi_properties '.$this->build_shortcode_atts($shortcode_atts).']');
    }

    protected function build_shortcode_atts($atts) {
        $output = '';
        foreach ($atts as $key => $value) {
            if (!empty($value)) {
                $output .= $key.'="'.esc_attr($value).'" ';
            }
        }
        return trim($output);
    }

    protected function content_template() {
        ?>
        <#
        var shortcode_atts = {
            limit: settings.limit,
            type: settings.property_types ? settings.property_types.join(',') : '',
            order: settings.order_by,
            pagination: settings.pagination,
            view_type: settings.view_type,
            show_filters: settings.show_filters,
            filters: settings.available_filters ? settings.available_filters.join(',') : '',
            details_button_text: settings.details_button_text,
            loadmore_button_text: settings.loadmore_button_text,
            filter_button_text: settings.filter_button_text
        };
        
        var shortcode = '[wasi_properties';
        _.each(shortcode_atts, function(value, key) {
            if (value) {
                shortcode += ' ' + key + '="' + value + '"';
            }
        });
        shortcode += ']';
        #>
        
        <div class="wasi-elementor-preview">
            <div class="elementor-shortcode">{{{ shortcode }}}</div>
            <div class="elementor-editor-notice">
                <?php echo __('Vista previa de propiedades Wasi', 'wasi-api'); ?>
            </div>
        </div>
        <?php
    }
}