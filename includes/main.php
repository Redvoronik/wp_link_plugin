<?php

require_once plugin_dir_path(__FILE__) . 'models/RelatedTable.php';

$listTable = new RelatedTable();
$listTable->prepare_items();

$few = $wpdb->get_results("SELECT article_id FROM wp_yandex_related GROUP BY article_id HAVING count(*) < 20");
$nothing = $wpdb->get_results("SELECT article_id FROM wp_yandex_related GROUP BY article_id HAVING count(*) = 0");
$count = $wpdb->get_results("SELECT count(*) as count FROM wp_yandex_related");
?>

<div class="wrap">
 <h1>Перелинковка</h1>
 <hr class="wp-header-end">
 <h2 class="screen-reader-text">Фильтровать список записей</h2>

 	 <ul class="subsubsub">
		<li class="all"><a href="/" class="current" aria-current="page">Все <span class="count">(<?= $count[0]->count ?>)</span></a> |</li>
		<li class="publish"><a href="edit.php?post_status=publish&amp;post_type=page">Необновленные <span class="count">(<?= count($nothing) ?>)</span></a> |</li>
		<li class="publish"><a href="edit.php?post_status=publish&amp;post_type=page">Менее 20 похожих <span class="count">(<?= count($few) ?>)</span></a></li>
	</ul>

<?php $listTable->display(); ?>
</div>
