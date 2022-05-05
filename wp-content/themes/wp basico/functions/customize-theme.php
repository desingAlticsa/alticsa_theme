<?php 
/**
 * Alticsa Customizer
 */
function alticsa_customize_register( $wp_customize ) {
    $wp_customize->add_section('theme_options', array(
        'title'    => __('alticsa opciones', 'alticsa'),
        'priority' => 130,
    ));
    $wp_customize->add_setting( 'logo_header', array(
        'transport' => 'refresh',
    ));
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'logo-header',
            array(
                'label'   => __('Upload a logo for header', 'alticsa'),
                'section' => 'theme_options',
            )
        )
    );
}

add_action( 'customize_register', 'alticsa_customize_register' );
?>