<?php

class YandexRelated
{
    private $site = null;
    private $user = null;
    private $key = null;

    private $query = null;
    private $ids = [];
    private $post = null;

    public static $table = 'yandex_related';

    function __construct(int $post_id) {
        $this->post = get_post($post_id);
        $this->site = get_option('site');
        $this->user = get_option('user');
        $this->key = get_option('key');
    }

    public function run() {
        $this::buildToYandexQuery();
        $this::getRelatedUrls();
        $this::saveRelatedArticles();
    }

    private function buildToYandexQuery()
    {
        $query = 'site:' . $this->site . ' '.$this->post->post_title;
        
        $params = [
            'user'  => $this->user,
            'key'   => $this->key,
            'query' => $query,
            'lr'    => 225,
            'sortby'    => 'rlv',
            'filter'    => 'none',
            'groupby'   => 'attr="".mode=flat.groups-on-page=40.docs-in-group=1',
        ];

        $buildQuery = http_build_query($params);

        $this->query = "https://yandex.ru/search/xml?" . $buildQuery;
        return $this->query;
    }

    private function getRelatedUrls() {
        $res = file_get_contents($this->query);

        $res = simplexml_load_string($res);

        $urls = $res->response->results->grouping->group;

        $ids = [];
        $j = 0;

        foreach ($urls as $result) {
            if (!empty($result->doc->url)) {
                if ($id = $this::getIdFromUrl($result->doc->url)) {
                    if($j >= 20) {
                        break;
                    }
                    if(!in_array($id, $ids)) {
                        $ids[] = $id;
                        $j++;
                    }
                }
            }
        }

        $this->ids = $ids;
        return $this->ids;
    }

    private function saveRelatedArticles()
    {
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        global $table_prefix;

        $post_id = $this->post->ID;

        $sql = "DELETE FROM {$table_prefix}" . self::$table . " WHERE article_id = " . $post_id;
        dbDelta($sql);

        foreach ($this->ids as $id) {
            $sql = "INSERT INTO {$table_prefix}" . self::$table . " (`article_id`, `related_article_id`) VALUES ( '$post_id', '$id')";

            dbDelta($sql);
        }
    }

    private function getIdFromUrl($url)
    {
        $link = parse_url($url);
        $link = ltrim($link['path'], '/');

        if($id = intval($link)) {
            return $id;
        }
        
        return false;
    }

    public static function getAll(string $param = null, $page = 1, $orderBy = 'id', $order = 'DESC')
    {
        global $wpdb, $table_prefix;

        $limit = 50;
        $offset = $limit * ($page-1);

        $where = " {$table_prefix}posts.post_type='post'";
        if($param == 'nothing') {
            $where = " {$table_prefix}posts.id IN (SELECT article_id FROM {$table_prefix}" . self::$table . " GROUP BY article_id HAVING count(*) = 0)";
        } elseif ($param == 'small') {
            $where = " {$table_prefix}posts.id IN (SELECT article_id FROM {$table_prefix}" . self::$table . " GROUP BY article_id HAVING count(*) < 20)";
        }

        return $wpdb->get_results("SELECT {$table_prefix}posts.id as id, {$table_prefix}posts.post_title as title, count(yandex_related.related_article_id) as count_related_article_id, {$table_prefix}posts.post_name as url, yandex_related.updated_at as updated_at FROM {$table_prefix}posts LEFT JOIN {$table_prefix}" . self::$table . " as yandex_related ON wp_posts.id = yandex_related.article_id WHERE" . $where . " GROUP BY {$table_prefix}posts.id ORDER BY {$table_prefix}posts." . $orderBy . " " . $order . " LIMIT " . $limit . " OFFSET " . $offset);
    }

    public static function getCount(string $query = null)
    {
        global $wpdb, $table_prefix;
        return $wpdb->get_results("SELECT count(*) as count FROM {$table_prefix}" . self::$table . " " . $query);
    }
}