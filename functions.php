<?php

/**
 * Estatik Project Theme functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link http://codex.wordpress.org/Theme_Development
 * @link http://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * @link http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Estatik_Theme_Project
 * @since Estatik Theme Project 1.0
 */

define( 'ES_PROJECT_DIR',  trailingslashit( get_template_directory()));
define( 'ES_PROJECT_URL',  trailingslashit( get_template_directory_uri()));
define( 'ES_PROJECT_INC_DIR',  ES_PROJECT_DIR  .trailingslashit( 'inc' ) );
define( 'ES_PROJECT_WIDGETS_DIR',  ES_PROJECT_INC_DIR  .trailingslashit( 'widgets' ) );
define( 'ES_PROJECT_WIDGETS_URL',  ES_PROJECT_URL  .trailingslashit( 'inc/widgets' ) );
define( 'ES_PROJECT_TEMPLATES_DIR',  ES_PROJECT_DIR  .trailingslashit( 'templates' ) );
define( 'ES_PROJECT_CUSTOM_STYLES_URL',  ES_PROJECT_URL  . trailingslashit( 'assets/css') );
define( 'ES_PROJECT_CUSTOM_STYLES_DIR',  ES_PROJECT_DIR  . trailingslashit( 'assets/css') );
define( 'ES_PROJECT_CUSTOM_SCRIPTS_URL', ES_PROJECT_URL  . trailingslashit('assets/js/custom' ) );
define( 'ES_PROJECT_VENDOR_SCRIPTS_URL', ES_PROJECT_URL  . trailingslashit('assets/js/vendor' ) );
define( 'ES_PROJECT_ADMIN_DIR',  ES_PROJECT_DIR  .trailingslashit( 'inc/admin' ) );
define( 'ES_PROJECT_ADMIN_IMAGES_URL',  ES_PROJECT_URL  . trailingslashit( 'inc/admin/assets/images') );
define( 'ES_PROJECT_ADMIN_CUSTOM_STYLES_URL',  ES_PROJECT_URL  . trailingslashit( 'inc/admin/assets/css/custom') );
define( 'ES_PROJECT_ADMIN_CUSTOM_SCRIPTS_URL', ES_PROJECT_URL  . trailingslashit('inc/admin/assets/js/custom' ) );
define( 'ES_PROJECT_ADMIN_TEMPLATES',          ES_PROJECT_DIR . trailingslashit('inc/admin/templates' ) );
define( 'ES_PROJECT_FONTS_URL',          ES_PROJECT_URL . trailingslashit('assets/fonts' ) );

require_once (ES_PROJECT_DIR . '/inc/class-project-theme.php');
remove_filter('the_content', 'wpautop');

/* init theme. */

Project_Theme::run();
