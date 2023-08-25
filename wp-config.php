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
define('DB_NAME', 'meyzjcmc_skorinbase');

/** MySQL database username */
define('DB_USER', 'meyzjcmc_skorinu');

/** MySQL database password */
define('DB_PASSWORD', 'skorin2016com');

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
define('AUTH_KEY',         'eoG 7aFbrQA_HYT~K#|C@i9@ioG9|V^={QdIn;Y>SS#?#eD#c(@R$ukncB6_9)CO');
define('SECURE_AUTH_KEY',  '>-tue[pi4%#).d&8Tqi`3OC5E$jZ:y3ibh{B2Ymm)RVjtU6PE9JoWiBhM W>8HX0');
define('LOGGED_IN_KEY',    'F6P.9aZR?46*D=`=W!![~tp[&g`h9gyANf)NAOu,n[lcuP4twG=I[@||uAr>v!zx');
define('NONCE_KEY',        'U5uW,hY/pJW;Y3T?T1}^rAd)#]`KA|x+P^q?iB0AdG|%7X}V,:24%]gTUbZ]i6T^');
define('AUTH_SALT',        'z)AtlAbbF|Y34>K8SXTZ)&J&C/a_c6t^f-g2)~O,!cZ4~N=~xRdQzxtU*%k)/8tx');
define('SECURE_AUTH_SALT', 'n]PzExZv#vPU)NLdhprK-cVA(iJ =SZhkSI-q2e|NtA.b[cr8^!<6-%W%x-2gUBa');
define('LOGGED_IN_SALT',   '[S7WkG}m$oIXQg7Zq`kp}Y<K;I_ 9w_{qEhYe:L~D,?{Wp#s0(&?2uz+Rn>;#hnP');
define('NONCE_SALT',       'w ;XB59r4h`:<[q_a-_{?>qd{&Hiq*Clf{42hQ)Nu~8L1qmc<1^HBJU$lsiTNDQO');

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
