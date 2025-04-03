<?php
if (!defined('ABSPATH')) exit;

class Elementor_Wasi_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'wasi-properties';
    }

    public function get_title() {
        return __('Propiedades Wasi', 'wasi-api');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['real-estate'];
    }

    protected function _register_controls() {
        // Sección de Configuración
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
                'label' => __('Cantidad de propiedades', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 50
            ]
        );

        $this->add_control(
            'property_type',
            [
                'label' => __('Tipo de propiedad', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                    '1' => __('Casas', 'wasi-api'),
                    '2' => __('Apartamentos', 'wasi-api'),
                    '3' => __('Terrenos', 'wasi-api')
                ],
                'multiple' => true,
                'label_block' => true
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Mostrar paginación', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'wasi-api'),
                'label_off' => __('No', 'wasi-api'),
                'return_value' => 'yes',
                'default' => 'no'
            ]
        );

        // Sección de Diseño
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Diseño', 'wasi-api'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columnas', 'wasi-api'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '2' => '2',
                    '3' => '3',
                    '4' => '4'
                ],
                'default' => '3'
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $shortcode_atts = [
            'limit' => $settings['limit'],
            'type' => !empty($settings['property_type']) ? implode(',', $settings['property_type']) : '',
            'pagination' => $settings['show_pagination'],
            'columns' => $settings['columns']
        ];

        $shortcode_string = '';
        foreach ($shortcode_atts as $key => $value) {
            if (!empty($value)) {
                $shortcode_string .= $key . '="' . esc_attr($value) . '" ';
            }
        }

        echo do_shortcode('[wasi_properties ' . trim($shortcode_string) . ']');
    }
}