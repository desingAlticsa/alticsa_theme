<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpalticsa' );

/** Database username */
define( 'DB_USER', 'diseno' );

/** Database password */
define( 'DB_PASSWORD', 'Alti0331$' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '7X;.&?Fk&bTz$3vd/r-B8#]=T)eIYiY S2g ~9k&FM3IxJjMI|oiJ),Y]/Il/np:' );
define( 'SECURE_AUTH_KEY',  'ey5OzyN| a+ya6(lAJMHdK68K(uY~V.Ccuuif{OyT-S&[N7m|j<;l1!9.L~8Y7*;' );
define( 'LOGGED_IN_KEY',    'LUq5H-IBYb*(vz2JX^~/SIJ^7Tz,n9uCDRI4j=zVyC]yRn,LnD*0?v*0fV~j;y{?' );
define( 'NONCE_KEY',        '8Nvq5.ZM|&(CI{p{#3B?LCf3}3Z1:t_kgE,e@af,J2v70x?n^LMW`$2 (T(0}rEs' );
define( 'AUTH_SALT',        'EZA}7Z!a<h ]?(^UM@.)W|H<Se bOkc:85%K_dfMM+o0mK KP-qA,P(OMIp%rvHI' );
define( 'SECURE_AUTH_SALT', '$Tb9DzF Dc/2q;;4p3>4KO-<y!eU~*zY}rJXr$yLjUx|?{NxXk6D21RP,WBgE8,F' );
define( 'LOGGED_IN_SALT',   'mq/*fFwFouZ-1mefLfl1@|:`Iv7%D0s}]p-n#93_:!=l!H8VD*-%NQfBCM:6eC2:' );
define( 'NONCE_SALT',       '*RpfTgQhw;#9`|25*V[zvHD%BOT:r7{NU7:k_K#+6)S^EjYw5tR3^m=O6B`ipzz6' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
