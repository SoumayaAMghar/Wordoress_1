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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '?|+Qx$C]lJ}2r!L Kv0uGTh7wni.Dh7Z]w,vQB2fEZ.+-4jBIn2nJG2mT^ 3sQw1' );
define( 'SECURE_AUTH_KEY',  '(7/R[hMvsb Gu8)M;:~A.gYnvik0~GR}OAz@^,Yt+]*3kEEs&mI<^J]/g 4*!AN+' );
define( 'LOGGED_IN_KEY',    'E42(izoqjyYI}j@TNDRX)|lt<AurJ5Xj[<!5##W^H]H!-`f)$yP2=.&S8Pu] qIC' );
define( 'NONCE_KEY',        '0S64Ctskm@tRkaA:]E*i5-amG<Ms-W0/@N^]1:^lw}SIe+e-r,bZ_61ti3bO!fRI' );
define( 'AUTH_SALT',        '4@k8lN%BZp6^PYpFwQ(I,-31k:W{IHMn~iQq>~s5.|D+lEW(X:oa {cV/;`=i9sF' );
define( 'SECURE_AUTH_SALT', '>K9TpBR8k_b,!Dc=w>TvdR;&5alSod~n(R6`MTg#b3|t|2J99=sn5!k_PUn2~`rL' );
define( 'LOGGED_IN_SALT',   ')%:CL5maC1$H0y_Ht.iBf&Uk{+ 1NA-(: H%;_;`9Y ut;D;gOHA3H}ID6,fmnD%' );
define( 'NONCE_SALT',       '.wi;VPoB,r*bTHK_GDck>Ja~I%#1L7=A!U_UK-6dK6KXcEi$ezF-5C&U_;a/brc,' );

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
