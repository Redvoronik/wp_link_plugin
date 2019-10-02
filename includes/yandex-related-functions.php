<?php

require_once plugin_dir_path(__FILE__) . 'YandexRelated.php';
require_once plugin_dir_path(__FILE__) . 'YandexRelatedWidget.php';

add_action('admin_menu', 'createLinkOnMainMenu');
add_action('admin_menu', 'createLinkOnParamasMenu');

register_setting('yandex-related-group', 'site');
register_setting('yandex-related-group', 'user');
register_setting('yandex-related-group', 'key');
register_setting('yandex-related-group', 'advert');

add_filter('the_content', array('YandexRelatedWidget', 'insert_related'));

function createDatabase()
{
    global $table_prefix, $wpdb;

    $tblname = 'yandex_related';
    $wp_track_table = $table_prefix . "$tblname ";

    if($wpdb->get_var( "show tables like $wp_track_table" ) != $wp_track_table) 
    {
    	$sql = "CREATE TABLE $wp_track_table (
			  id int(10) NOT NULL AUTO_INCREMENT,
			  article_id mediumint(5) NOT NULL,
			  related_article_id mediumint(5) NOT NULL,
			  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE KEY id (id)
			);";

        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
}