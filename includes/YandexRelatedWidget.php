<?php

class YandexRelatedWidget
{
    public static function insert_related($content) {

        global $post, $wpdb;
        $thePostID = $post->ID;

        $countPrs = substr_count($content, '<p');
        $prs = [
            2,
            round($countPrs/2),
            round(($countPrs*2)/3)
        ];

        $query = "SELECT wp_posts.post_title as title, wp_posts.post_name as url FROM wp_posts INNER JOIN wp_yandex_related ON wp_posts.id = wp_yandex_related.related_article_id WHERE wp_yandex_related.article_id = $thePostID";

        $posts = $wpdb->get_results($query);

        $posts = array_chunk($posts, 3);

        if ( is_single() && ! is_admin() ) {
            foreach ($prs as $key => $pr) {
                if(isset($posts[$key])) {
                    $similarBlock = static::renderRelated($posts[$key]);
                    $content = static::prefix_insert_after_paragraph($similarBlock, $pr, $content);
                }
            }
        }


        return $content;
    }

    private static function prefix_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );
        foreach ($paragraphs as $index => $paragraph) {
            if ( trim( $paragraph ) ) {
                $paragraphs[$index] .= $closing_p;
            }
            if ( $paragraph_id == $index + 1 ) {
                $paragraphs[$index] .= $insertion;
            }
        }
        return implode( '', $paragraphs );
    }

    private static function renderRelated(array $posts = []) {
        $advert = get_option('advert');
        ob_start();
        include 'similarArticles.html';
        return ob_get_clean();
    }
}