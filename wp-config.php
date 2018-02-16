<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpressdb');

/** MySQL database username */
define('DB_USER', 'wordpress_admin');

/** MySQL database password */
define('DB_PASSWORD', 'password');

/** MySQL hostname */
define('DB_HOST', 'localhost:3306');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'syKO:tQck$v1ER]au-N0UGGU*_S/S`/,y.rT<>+a@,O+15l!4$L;,n+JDfj|ZeVg');
define('SECURE_AUTH_KEY',  'f!fFF/_Qwo9uu6MJ0UOfcP:4q2BmP%#]gGI?3#D1GM[3^?`N5FvvTbC;FFkCCr<y');
define('LOGGED_IN_KEY',    '6}6To/~i)G$*iA<cQ7mI}KpJg/3nsKgQ]2l)aO8InK@VIw2%3S-~5oJ&PGBh#kz*');
define('NONCE_KEY',        '@_W0?h8.zLnIV83F BU6FIGQ/z<Ax?7#IBiOljU!(]4uQLu)K{~8, Z:,/o3~+Rx');
define('AUTH_SALT',        'T-DjJJiGh8fn0g%+a#n4n/,#7N9-WU@zTw]2kDK$O?xVIG!-^i&VhZaQ5DD#EK[ ');
define('SECURE_AUTH_SALT', 'a$2mU3;YdOaTxUu38{[EUlRFf2CUXBjO*kr}FL,Zh`qzK=-lsX;HBR[;{Pv^k8X3');
define('LOGGED_IN_SALT',   '.aV%IaP@)>@+<m(B]:~#u :Nxbl^N4R_+Zm(,R#4&V_3 Ff@;`n/`tc:r48xf!8L');
define('NONCE_SALT',       'LAK_sV[(EmE>88zsG0*sw|w2v??qqo:/Q,</xh{uQugqsF{sO0oTq;PDM+N}n]!r');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
