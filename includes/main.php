<?php
	$param 	= $_GET['param'] ?? null;
	$page 	= $_GET['paget'] ?? 1;

	$orderBy = $_GET['orderby'] ?? 'id';
	$order 	= $_GET['order'] ?? 'desc';

	$relateds 	= YandexRelated::getAll($param, $page, $orderBy, $order);
	$few 		= YandexRelated::getCount('GROUP BY article_id HAVING count(*) < 20');
	$nothing 	= YandexRelated::getCount('GROUP BY article_id HAVING count(*) = 0');
	$count 		= YandexRelated::getCount();

	$totalPages = round($count[0]->count / 50);

	$mainUrl = '/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php';
	$formUrl = $mainUrl . '&method=getRelated';

	$params = [
		'id' => 'ID статьи', 
		'title' => 'Заголовок', 
		'count_related_article_id' => 'Кол-во похожих', 
		'updated_at' => 'Обновлено'
	];
?>

<div>
	<h1>Перелинковка</h1>

	<div class="row">
		<ul class="subsubsub">
			<li class="all"><a href="<?= $mainUrl ?>" <?php if($param==null): ?>class="current"<?php endif; ?> aria-current="page">Все <span class="count">(<?= $count[0]->count ?>)</span></a> |</li>
			<li class="publish"><a <?php if($param=='nothing'): ?>class="current"<?php endif; ?> href="<?= $mainUrl ?>&param=nothing">Необновленные <span class="count">(<?= count($nothing) ?>)</span></a> |</li>
			<li class="publish"><a <?php if($param=='small'): ?>class="current"<?php endif; ?> href="<?= $mainUrl ?>&param=small">Менее 20 похожих <span class="count">(<?= count($few) ?>)</span></a></li>
		</ul>
	</div>
	
	<?php if(!empty($relateds)): ?>
		<table style="margin-top: 30px;" class="wp-list-table widefat fixed striped users">
		<thead>
		<tr>
			<?php foreach($params as $key => $value): ?>
			<th scope="col" id="<?= $key ?>" class="manage-column column-username column-primary sortable <?= ($key == $orderBy && $order == 'asc') ? 'desc' : 'asc' ?>">
				<a href="/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php&amp;orderby=<?= $key ?>&amp;order=<?= ($key == $orderBy && $order == 'asc') ? 'desc' : 'asc' ?>">
					<span><?= $value ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<?php endforeach; ?>
		</tr>
		</thead>

		<tbody id="the-list" data-wp-lists="list:user">
			
		<?php foreach($relateds as $related): ?>
			<tr>
				<td>
					<a target="_blank" href="/<?= $related->url ?>"><strong><?= $related->id ?></strong></a>
				</td>
				<td><?= $related->title ?></td>
				<td><strong><?= $related->count_related_article_id ?></strong><div class="row-actions">
						<span class="edit"><a href="<?= $formUrl ?>&id=<?= $related->id ?>">Обновить</a></div></td>
				<td><?= $related->updated_at ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="tablenav bottom">

					<div class="alignleft actions">
				</div>
		<div class="tablenav-pages"><span class="displaying-num"><?= $count[0]->count ?> элемент</span>
<span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<a class="prev-page button" href="/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php&amp;orderby=<?= $orderBy ?>&amp;order=<?= ($key == $orderBy && $order == 'asc') ? 'desc' : 'asc' ?>&amp;paget=<?= ($page > 1) ? $page-1 : 1 ?>"><span class="screen-reader-text">Предыдущая страница</span><span aria-hidden="true">‹</span></a>
<span class="screen-reader-text">Текущая страница</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><?= $page ?> из <span class="total-pages"><?= $totalPages ?></span></span></span>
<a class="next-page button" href="/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php&amp;orderby=<?= $orderBy ?>&amp;order=<?= ($key == $orderBy && $order == 'asc') ? 'desc' : 'asc' ?>&amp;paget=<?= ($page < $totalPages) ? $page+1 : $page ?>"><span class="screen-reader-text">Следующая страница</span><span aria-hidden="true">›</span></a>
<a class="last-page button" href="/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php&amp;orderby=<?= $orderBy ?>&amp;order=<?= ($key == $orderBy && $order == 'asc') ? 'desc' : 'asc' ?>&amp;paget=$totalPages"><span class="screen-reader-text">Последняя страница</span><span aria-hidden="true">»</span></a></span></div>
		<br class="clear">
	</div>
	<?php else: ?>
	<div style="margin-top: 70px;">
		<span>Ничего не найдено</span>
	</div>
	<?php endif;?>
</div>

