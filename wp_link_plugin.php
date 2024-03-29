<?php
/*
 * Plugin Name: Yandex похожие статьи
 * Description: Плагин для получения и работы с похожими статьями
 * Author:      SVteam
 * Version:     1.2
 */

require_once plugin_dir_path(__FILE__) . 'includes/models/YandexRelated.php';
require_once plugin_dir_path(__FILE__) . 'includes/models/YandexRelatedWidget.php';
require_once plugin_dir_path(__FILE__) . 'includes/models/InsertHelper.php';

add_action('admin_menu', 'createLinkOnMainMenu');

register_setting('wp_link_plugin-group', 'site');
register_setting('wp_link_plugin-group', 'user');
register_setting('wp_link_plugin-group', 'key');
register_setting('wp_link_plugin-group', 'advert');
register_setting('wp_link_plugin-group', 'firstblock');
register_setting('wp_link_plugin-group', 'secondblock');
register_setting('wp_link_plugin-group', 'thirdblock');
register_setting('wp_link_plugin-group', 'firstblockindex');
register_setting('wp_link_plugin-group', 'secondblockindex');
register_setting('wp_link_plugin-group', 'thirdblockindex');

add_filter('the_content', array('YandexRelatedWidget', 'insert_related'));

function createDatabase()
{
    global $table_prefix, $wpdb;

    $tblname = 'yandex_related';
    $wp_track_table = $table_prefix . "$tblname ";

    if($wpdb->get_var( "show tables like $wp_track_table" ) != $wp_track_table) 
    {
        $sql = "CREATE TABLE $wp_track_table (
              id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
              article_id bigint(20) UNSIGNED NOT NULL,
              related_article_id bigint(20) UNSIGNED NOT NULL,
              updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
            KEY `article_id` (`article_id`),
            KEY `related_article_id` (`related_article_id`)
            );";

        $wpdb->get_results($sql);

        $set_link = "ALTER TABLE $wp_track_table ADD CONSTRAINT `articles` FOREIGN KEY (`article_id`) REFERENCES `wp_posts`(`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT";

        $wpdb->get_results($set_link);

        $set_related_link = "ALTER TABLE $wp_track_table ADD CONSTRAINT `relateds` FOREIGN KEY (`related_article_id`) REFERENCES `wp_posts`(`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT";

        $wpdb->get_results($set_related_link);
    }
}

register_activation_hook( __FILE__, 'createDatabase' );

function createLinkOnMainMenu()
{
    add_menu_page(
        'Перелинковка yandex',
        'Перелинковка',
        'manage_options',
        'wp_link_plugin/includes/main.php',
        null,
        'dashicons-randomize'
    );

    add_submenu_page(
        'wp_link_plugin/includes/main.php',
		'Настройка', 
		'Настройка', 
		'manage_options',
		'wp_link_plugin/includes/params.php'
	);
}

if(isset($_GET['method']) && $_GET['method'] == 'getRelated' && $_GET['id']) {

    $yandexRelated = new YandexRelated($_GET['id']);
	$yandexRelated->run();

}

//CRON
add_action( 'admin_head', 'my_activation' );
function my_activation() {
    if( ! wp_next_scheduled( 'my_hourly_event' ) ) {
        wp_schedule_event( time(), 'hourly', 'my_hourly_event');
    }
}

add_action( 'my_hourly_event', 'do_this_hourly' );
function do_this_hourly(){
    $posts = YandexRelated::getCount('GROUP BY article_id HAVING count(*) = 0');
    foreach ($posts as $key => $post) {
        $yandexRelated = new YandexRelated($post->id);
        $yandexRelated->run();
    }
}