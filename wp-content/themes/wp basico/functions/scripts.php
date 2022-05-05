<?php 
function agregar_css_js() {
    wp_enqueue_style( 'style', get_stylesheet_uri());
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css' );
    wp_enqueue_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array ( 'jquery' ), '1.14', true);
    wp_enqueue_script( 'bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array ( 'popper' ), '4.3', true);
 //JS personalizado
 wp_enqueue_script( 'app.js', get_template_directory_uri() . '/assets/js/app.js', array('bootstrap-js'), '1.0', true );
  }
  add_action( 'wp_enqueue_scripts', 'agregar_css_js' );
  // Soporte imagenes destacadas
if  ( function_exists('add_theme_support')) {
    add_theme_support( 'post-thumbnails' );
}

?>