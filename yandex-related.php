<?php
/*
 * Plugin Name: Yandex похожие статьи
 * Description: Плагин для получения и работы с похожими статьями
 * Author:      SVteam
 * Version:     1.0
 */

require_once plugin_dir_path(__FILE__) . 'includes/yandex-related-functions.php';

register_activation_hook( __FILE__, 'createDatabase' );

function createLinkOnMainMenu()
{
    add_menu_page(
        'Перелинковка yandex',
        'Перелинковка',
        'manage_options',
        'yandex-related/includes/main.php'
    );

    add_menu_page(
		'Настройка перелинковки', 
		'Настройка перелинковки', 
		'manage_options',
		'yandex-related/includes/params.php'
	);
}

if($_GET['method'] && $_GET['method'] == 'getRelated' && $_POST['post_id']) {

    $yandexRelated = new YandexRelated($_POST['post_id']);
	$yandexRelated->run();

}