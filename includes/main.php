<?php
	$param 	= $_GET['param'] ?? null;
	$page 	= $_GET['page'] ?? 1;

	$relateds 	= YandexRelated::getAll($param);
	$few 		= YandexRelated::getCount('GROUP BY article_id HAVING count(*) < 20');
	$nothing 	= YandexRelated::getCount('GROUP BY article_id HAVING count(*) = 0');
	$count 		= YandexRelated::getCount();

	$mainUrl = '/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php';
	$formUrl = $mainUrl . '&method=getRelated';
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
	<div style="margin-top: 70px;">
		<table cellspacing="2" border="1" cellpadding="5" style="border-color: #AAA;">
			<thead>
				<th>id</th>
				<th>Заголовок</th>
				<th>Кол-во похожих</th>
				<th>Обновлено</th>
				<th>Действия</th>
			</thead>
			<tbody>
				<?php foreach($relateds as $related): ?>
					<tr>
						<td><?= $related->id ?></td>
						<td><a target="_blank" href="/<?= $related->url ?>"><?= $related->title ?></a></td>
						<td><?= $related->count_related_article_id ?></td>
						<td><?= $related->updated_at ?></td>
						<td>
							<form method="post" action="<?= $formUrl ?>">
								<input type="hidden" name="post_id" value="<?= $related->id ?>">
								<button type="submit">Обновить</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php else: ?>
	<div style="margin-top: 70px;">
		<span>Ничего не найдено</span>
	</div>
	<?php endif;?>
</div>

