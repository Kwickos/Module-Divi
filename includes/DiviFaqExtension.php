<?php

class DIVI_FAQ_Extension extends DiviExtension {

    public $gettext_domain = 'divi-faq-extension';
    public $name = 'divi-faq-extension';
    public $version = DIVI_FAQ_EXTENSION_VERSION;

    public function __construct( $name = 'divi-faq-extension', $args = array() ) {
        $this->plugin_dir     = trailingslashit(DIVI_FAQ_EXTENSION_PATH);
        $this->plugin_dir_url = trailingslashit(DIVI_FAQ_EXTENSION_URL);

        // Définir les modules disponibles
        $this->module_files = array(
            'DIVI_FAQ_Module' => $this->plugin_dir . 'includes/modules/FaqModule/FaqModule.php',
        );

        parent::__construct( $name, $args );
    }

    public function wp_hook_enqueue_scripts() {
        // Enqueue du JS compilé
        wp_enqueue_script(
            'divi-faq-module',
            $this->plugin_dir_url . 'dist/faq-module.js',
            array('react', 'react-dom'),
            $this->version,
            true
        );
    }
}

new DIVI_FAQ_Extension; 