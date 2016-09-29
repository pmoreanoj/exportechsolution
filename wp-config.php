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
define('DB_NAME', 'i2492970_wp1');

/** MySQL database username */
define('DB_USER', 'i2492970_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'C@F(diu1p]vyT#@qZ4*07#.1');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'n44nlzNuoI06MiNg2Mvykmw1nKAqIBAnBSEZLWTt3uXZEw70FEH0vqv7bZKaIuMK');
define('SECURE_AUTH_KEY',  'xUszfFAd46UH6lvMRyPwlmaW5S7Mz3NipDh9wsvPmALC9uFVhBQMOnzCUUuvozUZ');
define('LOGGED_IN_KEY',    'FcZjqboWVNQUvQel64iMZGwhXyJ61erh17lKQ78GosEty4X2HLJTdH4mQhPedldR');
define('NONCE_KEY',        'ktbzlU7O9YXZkH33JYjzDJlat0SxN9BlkyLGW5gGQtPWgDBFdZKFjFmCW72qEF7s');
define('AUTH_SALT',        'bH2KPF5nHlndYvc2gGGD48898cD10tBzlzmstqDLxIHDHwzCz6IwSZ4ucDObyA96');
define('SECURE_AUTH_SALT', 'CETw6KX6b9lMzyloSvkIO22nrZTJsNk444zTxpB8ls4kRhrXx238gonH8XJU5Bf3');
define('LOGGED_IN_SALT',   'MfSRlZpEXz1qWEoq2WpPQlSrcOHxI0iu55a35702LGJwRBn3NdnOjx5Du3KSv9SJ');
define('NONCE_SALT',       '5oF2MT7uMRA8Cz2UWuWmLnwkckEMejP73vwMFduU95dEWeDFhyXffLIGeVrXw9YB');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


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
