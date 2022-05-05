<?php 
// Menu en WordPres - Hace posible que se visualice el menu en personalización WP
function alticsa_register_my_menus() {
    register_nav_menus(
      array(
        'menu-principal' => __( 'Menu Superior' ),
        'menu-movil' => __( 'Menu movil' ),
        'menu-pie-pagina' => __( 'Menu pie de pagina' )
       )
     );
   }
  
   add_action( 'init', 'alticsa_register_my_menus' );
  
      require_once get_template_directory() . '/template-parts/navbar.php'; // personaliza Navbar
  /// Fin Menu WordPress
?>