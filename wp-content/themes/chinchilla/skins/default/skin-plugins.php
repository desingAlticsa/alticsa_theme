<?php
/**
 * Required plugins
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$chinchilla_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'chinchilla' ),
	'page_builders' => esc_html__( 'Page Builders', 'chinchilla' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'chinchilla' ),
	'socials'       => esc_html__( 'Socials and Communities', 'chinchilla' ),
	'events'        => esc_html__( 'Events and Appointments', 'chinchilla' ),
	'content'       => esc_html__( 'Content', 'chinchilla' ),
	'other'         => esc_html__( 'Other', 'chinchilla' ),
);
$chinchilla_theme_required_plugins        = array(
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'chinchilla' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'chinchilla' ),
		'required'    => true,
		'logo'        => 'trx_addons.png',
		'group'       => $chinchilla_theme_required_plugins_groups['core'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'chinchilla' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'elementor.png',
		'group'       => $chinchilla_theme_required_plugins_groups['page_builders'],
	),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'chinchilla' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'chinchilla' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $chinchilla_theme_required_plugins_groups['page_builders'],
	),
	'js_composer'                => array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'chinchilla' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'chinchilla' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'js_composer.jpg',
		'group'       => $chinchilla_theme_required_plugins_groups['page_builders'],
	),
	'woocommerce'                => array(
		'title'       => esc_html__( 'WooCommerce', 'chinchilla' ),
		'description' => esc_html__( "Connect the store to your website and start selling now", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'woocommerce.png',
		'group'       => $chinchilla_theme_required_plugins_groups['ecommerce'],
	),
	'elegro-payment'             => array(
		'title'       => esc_html__( 'Elegro Crypto Payment', 'chinchilla' ),
		'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'elegro-payment.png',
		'group'       => $chinchilla_theme_required_plugins_groups['ecommerce'],
	),
	'instagram-feed'             => array(
		'title'       => esc_html__( 'Instagram Feed', 'chinchilla' ),
		'description' => esc_html__( "Displays the latest photos from your profile on Instagram", 'chinchilla' ),
		'required'    => false,
        'install'     => false,
		'logo'        => 'instagram-feed.png',
		'group'       => $chinchilla_theme_required_plugins_groups['socials'],
	),
	'mailchimp-for-wp'           => array(
		'title'       => esc_html__( 'MailChimp for WP', 'chinchilla' ),
		'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'mailchimp-for-wp.png',
		'group'       => $chinchilla_theme_required_plugins_groups['socials'],
	),
	'booked'                     => array(
		'title'       => esc_html__( 'Booked Appointments', 'chinchilla' ),
		'description' => '',
		'required'    => false,
        'install'     => false,
		'logo'        => 'booked.png',
		'group'       => $chinchilla_theme_required_plugins_groups['events'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( 'The Events Calendar', 'chinchilla' ),
		'description' => '',
		'required'    => false,
        'install'     => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $chinchilla_theme_required_plugins_groups['events'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'chinchilla' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
	'latepoint'                  => array(
		'title'       => esc_html__( 'LatePoint', 'chinchilla' ),
		'description' => '',
		'required'    => false,
        'install'     => false,
		'logo'        => chinchilla_get_file_url( 'plugins/latepoint/latepoint.png' ),
		'group'       => $chinchilla_theme_required_plugins_groups['events'],
	),
	'advanced-popups'                  => array(
		'title'       => esc_html__( 'Advanced Popups', 'chinchilla' ),
		'description' => '',
		'required'    => false,
		'logo'        => chinchilla_get_file_url( 'plugins/advanced-popups/advanced-popups.jpg' ),
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
	'devvn-image-hotspot'                  => array(
		'title'       => esc_html__( 'Image Hotspot by DevVN', 'chinchilla' ),
		'description' => '',
		'required'    => false,
        'install'     => false,
		'logo'        => chinchilla_get_file_url( 'plugins/devvn-image-hotspot/devvn-image-hotspot.png' ),
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
	'ti-woocommerce-wishlist'                  => array(
		'title'       => esc_html__( 'TI WooCommerce Wishlist', 'chinchilla' ),
		'description' => '',
		'required'    => false,
		'logo'        => chinchilla_get_file_url( 'plugins/ti-woocommerce-wishlist/ti-woocommerce-wishlist.png' ),
		'group'       => $chinchilla_theme_required_plugins_groups['ecommerce'],
	),
	'twenty20'                  => array(
		'title'       => esc_html__( 'Twenty20 Image Before-After', 'chinchilla' ),
		'description' => '',
		'required'    => false,
        'install'     => false,
		'logo'        => chinchilla_get_file_url( 'plugins/twenty20/twenty20.png' ),
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
	'essential-grid'             => array(
		'title'       => esc_html__( 'Essential Grid', 'chinchilla' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'essential-grid.png',
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
	'revslider'                  => array(
		'title'       => esc_html__( 'Revolution Slider', 'chinchilla' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'revslider.png',
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
    'give' => array(
        'title'       => esc_html__( 'Give', 'chinchilla' ),
        'description' => '',
        'required'    => false,
        'logo'        => chinchilla_get_file_url( 'plugins/give/give.png' ),
        'group'       => $chinchilla_theme_required_plugins_groups['ecommerce'],
    ),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'chinchilla' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'chinchilla' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $chinchilla_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'Cookie Information', 'chinchilla' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $chinchilla_theme_required_plugins_groups['other'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'chinchilla' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'chinchilla' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $chinchilla_theme_required_plugins_groups['other'],
	),
);

if ( CHINCHILLA_THEME_FREE ) {
	unset( $chinchilla_theme_required_plugins['js_composer'] );
	unset( $chinchilla_theme_required_plugins['booked'] );
	unset( $chinchilla_theme_required_plugins['the-events-calendar'] );
	unset( $chinchilla_theme_required_plugins['calculated-fields-form'] );
	unset( $chinchilla_theme_required_plugins['essential-grid'] );
	unset( $chinchilla_theme_required_plugins['revslider'] );
	unset( $chinchilla_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $chinchilla_theme_required_plugins['trx_updater'] );
	unset( $chinchilla_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
chinchilla_storage_set( 'required_plugins', $chinchilla_theme_required_plugins );
