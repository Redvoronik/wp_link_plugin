<?php

class YandexRelated
{
    private $site = null;
    private $user = null;
    private $key = null;

    private $query = null;
    private $ids = [];
    private $post = null;

    function __construct(int $post_id) {
        $this->post = get_post($post_id);
        $this->site = get_option($site);
        $this->user = get_option($user);
        $this->key = get_option($key);
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
                    if($j >= 21) {
                        break;
                    }
                    $ids[] = $id;
                    $j++;
                }
            }
        }

        $this->ids = $ids;
        return $this->ids;
    }

    private function saveRelatedArticles()
    {
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

        $post_id = $this->post->ID;

        foreach ($this->ids as $id) {
            $sql = "INSERT INTO `wp_yandex_related` (`article_id`, `related_article_id`) VALUES ( '$post_id', '$id')";

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
}