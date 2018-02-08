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
define('DB_NAME', 'musicbox');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '0<i:?PKMqSWnUB2$$F$[s;ityo9ew3fs|#pQWQg)jDYB_#1Ns%[(`,PZx%8ZF7%a');
define('SECURE_AUTH_KEY',  'hvi Ah%3u8RkP:Qcy:/g{_B4HC&KXcYRa).Mm#rnJ(xv7H=@^E*LS^bgv%lY&JPA');
define('LOGGED_IN_KEY',    '}QXlMusKxpl2Q|Y{P,}3R#F=sYYSO7jw)%^5C-f+$~d;=$3$REv%f`mQ_4a%=./e');
define('NONCE_KEY',        '9V&0AG120Z5-!>GguZ#Y}/PY6#^|=1_b{{q%3>SO+#e6dB%pIW|Z+wUT+IFsb @Q');
define('AUTH_SALT',        'M~O$fS^?$G|!_f@WjtsAk9$Q*^e vcre?j nzan F[O<cdHQ!Q5}lHtwwbPS4Td?');
define('SECURE_AUTH_SALT', '[X*1EA!8lWu~8<d~nt?5ASDn8yv&4)_n]1IW;KSGGWNNC:U[[(|,D1<e--)Zg)tK');
define('LOGGED_IN_SALT',   'Vkx.I>f/b)#s:[_Rh7QN1jx*yA+$s9XImw=jOd|9QQ4R&k{R$bFZ6eUikM+>mVh=');
define('NONCE_SALT',       'mAA(7PU6PvPm`Ep>9[N>cL/hR<>G>niJ5MS><@8`};m>R26tnmnB8-t1_$ANekXG');

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

