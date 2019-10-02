<div class="wrap">
<h2>Настройки перелинковки</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Url сайта</th>
<td><input type="text" name="site" value="<?php echo get_option('site'); ?>" /></td>
</tr>
 
<tr valign="top">
<th scope="row">Пользователь Yandex</th>
<td><input type="text" name="user" value="<?php echo get_option('user'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Ключ Yandex</th>
<td><input type="text" name="key" value="<?php echo get_option('key'); ?>" /></td>
</tr>

</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="site,user,key" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>