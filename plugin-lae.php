<?php
/*
Plugin Name: Recolector de banners de Loterías y Apuestas del Estado
Plugin URI: https://github.com/LiThaM/WP-LogosLAE
Description: Este plugin captura las URLs de los banners de Loterías y Apuestas del Estado en tu sitio web.
Version: 1.2.0
Author: Alejandro
Author URI: https://github.com/LiThaM
License: GPL2
*/


// Define la constante de versión del plugin
defined( 'BANNERS_LAE_VERSION' ) or define( 'BANNERS_LAE_VERSION', '1.2.0' );

/**
 * Obtiene las URLs de los banners de Loterías y Apuestas del Estado.
 *
 * @return array|bool Un array con las URLs de los banners o false si falla la conexión o el parsing HTML.
 */
function obtener_banners() {
    $html = @file_get_contents('https://www.loteriasyapuestas.es/es');
    if ( false === $html ) {
        return false;
    }

    if ( ! class_exists( 'simple_html_dom' ) ) {
        require_once( plugin_dir_path( __FILE__ ) . 'simple_html_dom.php' );
    }
    $dom = new simple_html_dom();
    $success = $dom->load( $html );
    if ( ! $success ) {
        return false;
    }

    $banner_urls = [];
    for ($i = 1; $i <= 4; $i++) {
        $img_element = $dom->find('#item-banner-' . $i, 0);

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

    if ( false === $banners ) {
        echo 'Error: no se pueden obtener los banners de Loterías y Apuestas del Estado';
        return;
    }

    if (empty($banners)) {
        echo 'No se encontraron banners.';
        return;
    }

    $html = '<div class="slick-slider">';

    foreach ($banners as $banner) {
        $html .= '<img src="' . esc_url($banner) . '" />';
    }

    $html .= '</div>';

        // Agrega los archivos CSS y JS de Slick Slider
        wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
        wp_enqueue_script('jquery');
        wp_enqueue_script('slick-slider', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);

        // Agrega el código HTML del carrusel de banners y el script de Slick Slider
        $html .= '<script>jQuery(document).ready(function() { jQuery(".slick-slider").slick({autoplay: true, autoplaySpeed: 1000, dots: true,  slidesToShow: 1,}); });</script>';
        echo $html;
}

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
