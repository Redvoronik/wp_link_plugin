<?php
	// if ( ! class_exists( 'WP_List_Table' ) ) {
	// 	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	// }

	$similars = $wpdb->get_results("SELECT wp_posts.id as id, wp_posts.post_title as title, count(wp_yandex_related.related_article_id) as count_related_article_id, wp_posts.post_name as url, wp_yandex_related.updated_at as updated_at FROM wp_posts INNER JOIN wp_yandex_related ON wp_posts.id = wp_yandex_related.article_id GROUP BY wp_yandex_related.article_id");

	$few = $wpdb->get_results("SELECT article_id FROM wp_yandex_related GROUP BY article_id HAVING count(*) < 20");

	// class Customers_List extends WP_List_Table {

	// /** Class constructor */
	// public function __construct() {

	// 	parent::__construct( [
	// 		'singular' => __( 'Customer', 'sp' ), //singular name of the listed records
	// 		'plural'   => __( 'Customers', 'sp' ), //plural name of the listed records
	// 		'ajax'     => false //should this table support ajax?

	// 	] );

	// }
	
?>

<div class="wrap">
 <h1>Перелинковка</h1>
 <span>Статьи для которых собрано менее 20 похожих: <?= count($few) ?> <button>Показать</button></span>
 <hr>
 <table>
 	<thead>
 		<th>id</th>
 		<th>Заголовок</th>
 		<th>Основной запрос</th>
 		<th>Категория</th>
 		<th>Собрано похожих</th>
 		<th>Обновлено</th>
 		<th>Управление</th>
 	</thead>
 	<tbody align="center">
 	<?php foreach($similars as $similar): ?>
 		<tr>
 			<td><?= $similar->id ?></td>
 			<td><a target="_blank" href="/<?= $similar->url ?>"><?= $similar->title ?></a></td>
 			<td></td>
 			<td></td>
 			<td><?= $similar->count_related_article_id ?></td>
 			<td><?= $similar->updated_at ?></td>
 			<td>
 				<form method="post" action="/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php&method=getRelated">
 					<input type="hidden" name="post_id" value="<?= $similar->id ?>">
 					<button type="submit">Обновить</button>
 				</form>
 			</td>
 		</tr>
 	<?php endforeach; ?>
 	</tbody>
 </table>
</div>
