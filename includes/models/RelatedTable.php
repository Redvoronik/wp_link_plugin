<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class Simple_WP_List_Table extends WP_List_Table {
	
	private $per_page = 5;
	
	function __construct($options, $per_page = NULL) {
		global $status, $page;
		
		parent::__construct($options);
		
		if ($per_page) {
			$this->per_page = $per_page;
		}
	}

	function column_default($item, $column_name){
		return $item[$column_name];
	}


	function prepare_items(string $param = null) {
		global $wpdb;

		$where = null;
		if($param == 'nothing') {
			$where = ' WHERE wp_posts.id IN (SELECT article_id FROM wp_yandex_related GROUP BY article_id HAVING count(*) = 0)';
		} elseif ($param == 'small') {
			$where = ' WHERE wp_posts.id IN (SELECT article_id FROM wp_yandex_related GROUP BY article_id HAVING count(*) < 20)';
		}

		$query = "SELECT wp_posts.id as id, wp_posts.post_title as title, count(wp_yandex_related.related_article_id) as count_related_article_id, wp_posts.post_name as url, wp_yandex_related.updated_at as updated_at FROM wp_posts INNER JOIN wp_yandex_related ON wp_posts.id = wp_yandex_related.article_id " . $where . " GROUP BY wp_yandex_related.article_id ORDER BY wp_posts.id DESC";
	
		$total_items = $wpdb->query($query); 
		
		$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		
		if(!empty($paged)){
			$offset=($paged-1)*$this->per_page;
			$query.=' LIMIT '.(int)$offset.','.(int)$this->per_page;
		}
			
		
		$current_page = $this->get_pagenum();
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => ceil($total_items/$this->per_page),
			'per_page' => $this->per_page,
		) );

		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		if($sortable > 1)
			$this->_column_headers = array($columns, $hidden, $sortable);		   

		
		$this->process_bulk_action();
		
		
		$this->items = $wpdb->get_results($query, 'ARRAY_A');
	}
	
	function display() {
		?>
		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php parent::display(); ?>
		</form>
		<?php
	}
}


class RelatedTable extends Simple_WP_List_Table {

	function __construct() {
		parent::__construct(array(
			'singular'  => 'gallery',   
			'plural'    => 'galleries',  
			'ajax'      => false   
		), 5);
	}

	function get_columns() {
		return array(
			'id' => 'ID статьи',
			'title' => 'Заголовок',	   
			'count_related_article_id' => 'Кол-во похожих',
			'updated_at' => 'Дата обновления',
			'name' => 'Действия'
		);
	}


	public function get_sortable_columns() {
		return array(
			'title' => array('title'),
			'count_related_article_id' => array('count_related_article_id')
		);
	}
	
	function column_name($item){

		$actions = array(
			'edit'      => '<form method="post" action="/wp-admin/admin.php?page=yandex-related%2Fincludes%2Fmain.php&method=getRelated">
 					<input type="hidden" name="post_id" value="' . $item['id'] . '">
 					<button type="submit">Обновить</button>
 				</form>'
		);

		return $item['name'].' '.$this->row_actions($actions);
	}	

}