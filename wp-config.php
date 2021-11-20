<?php
define( 'WP_CACHE', false ); // Added by WP Rocket
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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );
/** MySQL database username */
define( 'DB_USER', 'root' );
/** MySQL database password */
define( 'DB_PASSWORD', 'root' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'AMOSkmG3zFPvyPgBM9zhSUzARWBpPwAS5b6TyAEYDmf94ROJ8b7pzqNG2azK4WvpM6dUL22AZNsg9T+89iAADA==');
define('SECURE_AUTH_KEY',  'NJP/JBZrbb2U4MNfG4RXHJglaJ2b5EYYZF7aWHw9xqoT9oWpTReUnc3NED2u48xNJrb1NAw5MFP86VCj7vn2uQ==');
define('LOGGED_IN_KEY',    'rysViOr+ExmEg2mtU0ioRMoRDdi3CtOlrgbPxUEnabtcUEt06fgeQCr+o5klFTBC/9N3edWmgjpC+TXSAmCp5A==');
define('NONCE_KEY',        '9XJkVWTubFwRJPJVRR62bXajofz3LVOcP9Zgbyhj2Ramkh84T8M3PB54rvkpFcOmZhC8J1JjOBx6uuiv+0tklQ==');
define('AUTH_SALT',        '5gbQYlHmVV/w83mXYZr3JrxeJaG1qJqZqqYUmHVBsq6PLyGTH0cADi0hChjOYbmkq5V60atpVtljbDvGevI+SQ==');
define('SECURE_AUTH_SALT', 'jUsPNjbyFS08mJ1rowGBczQPKZmiDVDt8LJKtwypgm9fCdGGZ1VS2yTqrn4DeUf+/iy1xszutq8BY40Eiv303Q==');
define('LOGGED_IN_SALT',   'ds2+z7IW3Y04z2BRCs2SqekFP9jKXav8aIF83lnRskAYQju8jJCuu6C23BA48cyp0qsUAsFhuWVnn0TagEkgcQ==');
define('NONCE_SALT',       'o7EWg58gDbCXD/YLY0zTT2jNzaFos6bbzw80vGvQ8ntw0H9eHFQkQelq18GhyHJVLqx9kyY6djvkn/NTbdnQog==');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
