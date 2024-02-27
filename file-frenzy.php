<?php
/*
** Plugin Name: File Frenzy
** Plugin URI: http://wordpress.org/plugins/file-frenzy/
** Text Domain: file-frenzy
** Description: Display files from your server directories in sortable data tables.
** Version: 1
** Author: Daan Oostindiën
** Author URI: https://oostindien.eu
** License: MIT
*/



define( 'FILEFRENZY_PLUGIN', __FILE__ );
define( 'FILEFRENZY_PLUGIN_DIR', untrailingslashit( dirname( FILEFRENZY_PLUGIN ) ) );
define( 'FILEFRENZY_PLUGIN_CLASSES', WP_PLUGIN_DIR . '/file-frenzy/Class');
define( 'FILEFRENZY_URL', str_replace('\\','/', plugins_url('', FILEFRENZY_PLUGIN)));
define( 'FILEFRENZY_VERSION', 1);

require_once FILEFRENZY_PLUGIN_DIR . '/load.php';

add_shortcode('filefrenzy', 'fileList');
function fileList($atts){
    $filelist = new FileFrenzy($atts);
    return $filelist->display();
}


