<?php

class DIVI_FAQ_Module extends ET_Builder_Module {

    public $slug       = 'divi_faq';
    public $vb_support = 'on';
    public $fullwidth  = true;
    public $icon       = '3';

    protected $module_credits = array(
        'module_uri' => '',
        'author'     => '',
        'author_uri' => '',
    );

    public function __construct() {
        parent::__construct();
        $this->name = esc_html__( 'FAQ Accordéon', 'divi-faq-extension' );
        $this->main_css_element = '%%order_class%%';
        
        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => array(
                        'title' => esc_html__( 'Contenu', 'divi-faq-extension' ),
                        'priority' => 10,
                    ),
                ),
            ),
        );

        // Ajout des styles CSS
        $this->custom_css_fields = array(
            'faq_title' => array(
                'label'    => esc_html__( 'Titre FAQ', 'divi-faq-extension' ),
                'selector' => '%%order_class%% .et_pb_toggle_title',
            ),
            'faq_content' => array(
                'label'    => esc_html__( 'Contenu FAQ', 'divi-faq-extension' ),
                'selector' => '%%order_class%% .et_pb_toggle_content',
            ),
        );
    }

    public function get_fields() {
        return array(
            'categories' => array(
                'label'           => esc_html__( 'Catégories FAQ', 'divi-faq-extension' ),
                'type'            => 'multiple_checkboxes',
                'option_category' => 'basic_option',
                'options'         => $this->get_faq_categories(),
                'description'     => esc_html__( 'Sélectionnez les catégories à afficher', 'divi-faq-extension' ),
                'toggle_slug'     => 'main_content',
                'default'         => 'on',
            ),
            'show_category_title' => array(
                'label'           => esc_html__( 'Afficher les titres des catégories', 'divi-faq-extension' ),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__( 'Oui', 'divi-faq-extension' ),
                    'off' => esc_html__( 'Non', 'divi-faq-extension' ),
                ),
                'default'         => 'on',
                'toggle_slug'     => 'main_content',
            ),
            'open_first_item' => array(
                'label'           => esc_html__( 'Ouvrir le premier élément', 'divi-faq-extension' ),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__( 'Oui', 'divi-faq-extension' ),
                    'off' => esc_html__( 'Non', 'divi-faq-extension' ),
                ),
                'default'         => 'off',
                'toggle_slug'     => 'main_content',
            ),
        );
    }

    private function get_faq_categories() {
        $categories = get_terms( array(
            'taxonomy'   => 'faq_category',
            'hide_empty' => false,
        ) );

        $options = array();
        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $options[ $category->term_id ] = $category->name;
            }
        }

        return $options;
    }

    public function get_advanced_fields_config() {
        return array(
            'fonts' => array(
                'header' => array(
                    'label'    => esc_html__( 'Titre', 'divi-faq-extension' ),
                    'css'      => array(
                        'main' => "{$this->main_css_element} .et_pb_toggle_title",
                    ),
                    'font_size' => array(
                        'default' => '16px',
                    ),
                    'line_height' => array(
                        'default' => '1.7em',
                    ),
                ),
                'body'   => array(
                    'label'    => esc_html__( 'Contenu', 'divi-faq-extension' ),
                    'css'      => array(
                        'main' => "{$this->main_css_element} .et_pb_toggle_content",
                    ),
                    'font_size' => array(
                        'default' => '14px',
                    ),
                    'line_height' => array(
                        'default' => '1.7em',
                    ),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
            ),
            'border' => array(),
            'custom_margin_padding' => array(
                'css' => array(
                    'important' => 'all',
                ),
            ),
        );
    }

    public function render($attrs, $render_slug, $content = null) {
        // Assurez-vous que les props sont définies
        $categories = isset( $this->props['categories'] ) ? $this->props['categories'] : '';
        $show_category_title = isset( $this->props['show_category_title'] ) ? $this->props['show_category_title'] : 'on';
        $open_first_item = isset( $this->props['open_first_item'] ) ? $this->props['open_first_item'] : 'off';

        // Générer un ID unique pour ce module
        $module_id = $this->module_id();

        return sprintf(
            '<div id="%1$s" class="et_pb_module divi-faq-module" data-categories="%2$s" data-show-category-title="%3$s" data-open-first-item="%4$s"></div>',
            esc_attr($module_id),
            esc_attr($categories),
            esc_attr($show_category_title),
            esc_attr($open_first_item)
        );
    }
}

new DIVI_FAQ_Module; 