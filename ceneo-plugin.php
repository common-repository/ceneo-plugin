<?php
/*
Plugin Name: Ceneo Plugin
Plugin URI: http://info.ceneo.pl/o-ceneo/wtyczki-do-pobrania
Description: Dodaj widgety Ceneo do swojego bloga!
Author: ceneo.pl
Version: 1.0
Author URI: http://www.ceneo.pl
*/

define('CENEO_PLUGIN_URL'       , plugin_dir_url( __FILE__ ));              // Defining plugin url path.
define('CENEO_PLUGIN_MAINFILE'  , __FILE__);                                // Defining plugin main filename.
define('CENEO_CSS_FOLDER'       , 'css/');                                  // Defining path for CSS stylesheets.
define('CENEO_WIDGETS_FOLDER'   , 'widgets/');                              // Defining path for widgets.
define('CENEO_SETTINGS_FOLDER'  , 'settings/');                             // Defining path for settings.
define('CENEO_ADMIN_CSS'        , CENEO_CSS_FOLDER    . 'ceneo_admin.css');     // Defining path for administration stylesheet.
define('CENEO_WIDGET_CSS'       , CENEO_CSS_FOLDER    . 'ceneo_widget.css');    // Defining path for administration widget stylesheet.
define('CENEO_THEME_CSS'        , CENEO_CSS_FOLDER    . 'ceneo_theme.css');     // Defining path for theme widget stylesheet.
define('CENEO_TRANSLATION_ID'   , 'ceneo-lang');                              // Defining gettext translation ID.

require_once(CENEO_SETTINGS_FOLDER  . 'admin_menu.php');
//------------------------------------------------------------
					     //WIDGETS
//------------------------------------------------------------
require_once(CENEO_WIDGETS_FOLDER  . 'popular_products_vertical.php'); 
require_once(CENEO_WIDGETS_FOLDER  . 'popular_products_horizontal.php'); 

global $ceneo_api_default_api_key;
global $ceneo_api_endpoint;
global $ceneo_api_method_for_products;
global $ceneo_api_method_for_categories;
$ceneo_api_default_api_key = 'ff76f08d-c4bc-4319-ae1a-080951c10111';
$ceneo_api_endpoint = 'http://developers.ceneo.pl/api/v2/function/';
$ceneo_api_method_for_products = 'webapi_data.getProductsByID';
$ceneo_api_method_for_categories = 'webapi_data.getPopularProductsByCategoryName';

function ceneo_plugin_load_widgets() {
  global $ceneo_plugin_settings;
  
  $ceneo_plugin_settings = get_option('ceneo_plugin_options');
  
  register_widget('Ceneo_Plugin_Popular_Products_Horizontal');
  register_widget('Ceneo_Plugin_Popular_Products_Vertical');
}

function ceneo_plugin_load_scripts_into_widgets() {
  wp_register_style('ceneo_plugin_style', CENEO_WIDGET_CSS);
    wp_enqueue_style('ceneo_plugin_style');
echo '<link rel="stylesheet" href="'. CENEO_PLUGIN_URL .CENEO_THEME_CSS.'" type="text/css" media="all" />';
}

add_action('widgets_init', 'ceneo_plugin_load_widgets');
//-------------------------------------------------------------

add_action('admin_init'              , 'ceneo_plugin_admin_init');                                // Defining actions on admin page init.
add_action('admin_menu'              , 'ceneo_plugin_admin_menu');

add_action('wp_head', 'ceneo_plugin_load_scripts_into_widgets');

?>