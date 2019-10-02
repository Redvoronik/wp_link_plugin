<div class="wrap">
	<h2>Настройки</h2>

	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>

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

		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="site,user,key,advert" />

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
</div>