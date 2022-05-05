<?php 
function alticsa_custom_logo_setup() {
    $defaults = array(
        'width'                => 200,
        'height'               => 100,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-text'          => array( 'site-title', 'site-description' ),
        'unlink-homepage-logo' => true, 
    );
  
    add_theme_support( 'custom-logo', $defaults );
  }
  
  add_action( 'after_setup_theme', 'alticsa_custom_logo_setup' );
  
  // encabezado 
  // add_theme_support( 'custom-header' );
  // function themename_custom_header_setup() {
  //   $defaults = array(
  //       // Default Header Image to display
  //       'default-image'         => get_template_directory_uri() . '/images/headers/default.jpg',
  //       // Display the header text along with the image
  //       'header-text'           => false,
  //       // Header text color default
  //       'default-text-color'        => '000',
  //       // Header image width (in pixels)
  //       'width'             => 200,
  //       // Header image height (in pixels)
  //       'height'            => 100,
  //       // Header image random rotation default
  //       'random-default'        => false,
  //       // Enable upload of image file in admin 
  //       'uploads'       => false,
  //       // function to be called in theme head section
  //       'wp-head-callback'      => 'wphead_cb',
  //       //  function to be called in preview page head section
  //       'admin-head-callback'       => 'adminhead_cb',
  //       // function to produce preview markup in the admin screen
  //       'admin-preview-callback'    => 'adminpreview_cb',
  //       );
  // }
  // add_action( 'after_setup_theme', 'themename_custom_header_setup' );
?>