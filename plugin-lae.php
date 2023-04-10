<?php
/*
Plugin Name: Recolector de banners de Loterías y Apuestas del Estado
Plugin URI: https://github.com/LiThaM/WP-LogosLAE
Description: Este plugin captura las URLs de los banners de Loterías y Apuestas del Estado en tu sitio web.
Version: 1.1.0
Author: Alejandro
Author URI: https://github.com/LiThaM
License: GPL2
*/

// Define la constante de versión del plugin
define('BANNERS_LAE_VERSION', '1.1.0');

// Incluye la librería Simple HTML DOM solo si no está incluida ya
if (!class_exists('simple_html_dom')) {
    require_once(plugin_dir_path(__FILE__) . 'simple_html_dom.php');
}

/**
 * Obtiene las URLs de los banners de Loterías y Apuestas del Estado.
 *
 * @return array Un array con las URLs de los banners.
 */
function obtener_banners() {
    $html = file_get_html('https://www.loteriasyapuestas.es/es');
    $banner_urls = [];

    for ($i = 1; $i <= 4; $i++) {
        $img_element = $html->find('#item-banner-' . $i, 0);

        if ($img_element) {
            $url = $img_element->find('img', 0)->getAttribute('data-src-pc');

            if ($url) {
                $banner_urls[] = 'https://www.loteriasyapuestas.es' . $url;
            }
        }
    }

    return $banner_urls;
}

/**
 * Muestra los banners en un carrusel utilizando Slick Slider.
 */
function mostrar_banners() {
    $banners = obtener_banners();

    if (!empty($banners)) {
        $html = '<div class="slick-slider">';

        foreach ($banners as $banner) {
            $html .= '<img src="' . esc_url($banner) . '" />';
        }

        $html .= '</div>';

        // Agrega los archivos CSS y JS de Slick Slider
        wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css');
        wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css');
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'slick-slider', 'https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array('jquery'), '1.6.0', true );
        wp_add_inline_script( 'slick-slider', 'jQuery.noConflict();', 'before' );

        // Agrega el código HTML del carrusel de banners y el script de Slick Slider
        $html .= '<script>jQuery(".slick-slider").slick({autoplay: true, autoplaySpeed: 5000});</script>';
        echo $html;
    } else {
        echo 'No se encontraron banners.';
    }
}

function add_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'slick-slider', 'https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array('jquery'), '1.6.0', true );
    wp_add_inline_script( 'slick-slider', 'jQuery.noConflict();', 'before' );
}
add_action( 'wp_enqueue_scripts', 'add_scripts' );


/**
 * Crea el shortcode [banners_lae] para mostrar los banners.
 *
 * @return string El código HTML del carrusel de banners.
 */
function shortcode_banners() {
    ob_start();
    mostrar_banners();
    return ob_get_clean();
}
add_shortcode('banners_lae', 'shortcode_banners');
