<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ababkova_26820' );

/** Database username */
define( 'DB_USER', 'ababkova_26820' );

/** Database password */
define( 'DB_PASSWORD', 'f31c39d6a37518c3589c' );

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
define( 'AUTH_KEY',         'LJFSe|H:n]V1oAhPFL0b3BH;)jGJpe7h{E`}bB/+0.(low0--|08HQ+j27Jgl|Vu' );
define( 'SECURE_AUTH_KEY',  '_p%R>fYXsK8O70=6z<CG-/geOG^^rB4-1 Ee)rXd{M4We;K;s+a<ebP!P;r,_9|[' );
define( 'LOGGED_IN_KEY',    'M~m96I@[*X7LQn`V88?NX*BCV[x,Ko_]#7d&svrbi>z[7(-0X:,ne3+WZzKh.0?j' );
define( 'NONCE_KEY',        '`ZuW!OmW%bOv#*AK$Kwz{`SCJqR7a&(i)Y=Z)IQb`|uv.)^&X(g8x(1ASt<{JOea' );
define( 'AUTH_SALT',        '=GJkl/H*910vZtx{g|:1;(-z/[q/+?LL>3r0?8GN|mLU}aN_m$idcl8SV/{ }z%X' );
define( 'SECURE_AUTH_SALT', '7&sb9TbD0_=/bbGEp%#<.0VPZi!ZO}ewx_o2ewJNoK|-r1S|6, XG -}_-(Dm|)D' );
define( 'LOGGED_IN_SALT',   '9j}o9~AbPHf?N4j06^F ,%WM~mkOQ(&3%@J)ya@mLG IT4 y|n`b`O8-_IH^iZiR' );
define( 'NONCE_SALT',       '.SbYIR_GBNfI=.<Gb3!5IwI=@9`e!sKn:* E7~e#:+Ic!@KY_4Rs&O#4AQtM@2Zk' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_caaQ3_';


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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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