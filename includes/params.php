<div class="wrap">
	<h2>Настройки</h2>

	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<div class="postbox">
			<div class="inside" style="display: block;margin-right: 12px;">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Url сайта</th>
						<td><input type="text" name="site" value="<?php echo get_option('site'); ?>" /><p class="description">Ссылка на сайте, в формате <strong>sitename.domain</strong></p></td>
					</tr>
					<tr valign="top">
						<th scope="row">Пользователь Yandex</th>
						<td><input type="text" name="user" value="<?php echo get_option('user'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Ключ Yandex</th>
						<td>
							<input type="text" name="key" value="<?php echo get_option('key'); ?>" />
							<p class="description">Ключ доступен в настройках <a target="_blank" href="https://xml.yandex.ru/settings/">XML Yandex</a></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Блок рекламы</th>
						<td>
							<textarea name="advert"><?php echo get_option('advert'); ?></textarea>
							<p class="description">Контент отображающийся в блоке рядом с ссылками перелинковки</p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<div class="inside" style="display: block;margin-right: 12px;">
				<p>В расположении указывается либо целое число, для вывода блока после конкретного параграфа.  Либо дробное (2/3 и т.п.), для относительного расположения.</p>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="firstblock">Первый блок перелинковки</label></th>
						<td><label><input name="firstblock" type="checkbox" id="firstblock" class="regular-text" <?= (get_option('firstblock')) ? 'checked="checked"' : null ?>>Отображать</label></td>
						<td>
							<input type="text" name="firstblockindex" value="<?php echo get_option('firstblockindex'); ?>" />
							<p class="description">Расположение</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="secondblock">Второй блок перелинковки</label></th>
						<td><label><input name="secondblock" type="checkbox" id="secondblock" class="regular-text" <?= (get_option('secondblock')) ? 'checked="checked"' : null ?>>Отображать</label></td>
						<td>
							<input type="text" name="secondblockindex" value="<?php echo get_option('secondblockindex'); ?>" />
							<p class="description">Расположение</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="thirdblock">Третий блок перелинковки</label></th>
						<td><label><input name="thirdblock" type="checkbox" id="thirdblock" class="regular-text" <?= (get_option('thirdblock')) ? 'checked="checked"' : null ?>>Отображать</label></td>
						<td>
							<input type="text" name="thirdblockindex" value="<?php echo get_option('thirdblockindex'); ?>" />
							<p class="description">Расположение</p>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="site,user,key,advert,firstblock,secondblock,thirdblock,firstblockindex,secondblockindex,thirdblockindex" />

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
</div>