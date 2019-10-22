<?php

class YandexRelatedWidget
{
    public static function insert_related($content) {
        global $post, $wpdb;
        $thePostID = $post->ID;

        $countPrs = substr_count($content, '<p');
  
        $query = "SELECT wp_posts.post_title as title, wp_posts.post_name as url FROM wp_posts INNER JOIN wp_yandex_related ON wp_posts.id = wp_yandex_related.related_article_id WHERE wp_yandex_related.article_id = $thePostID";

        $posts = $wpdb->get_results($query);
        $posts = array_chunk($posts, 3);

        if ( ! is_admin() ) {
            if(isset($posts[0])) {
                $insertHelper = new InsertHelper();
                $insertHelper->content = $content;
                $insertHelper->bannerHtml = static::renderRelated($posts[0]);
                $insertHelper->paragraphId = 1;
                $content = $insertHelper->run();
            }
            
            if(isset($posts[1])) {
                $insertHelper->content = $content;
                $insertHelper->bannerHtml = static::renderRelated($posts[1]);
                $insertHelper->adaptiveIndex = 1/2;
                $content = $insertHelper->run();
            }

            if(isset($posts[2])) {
                $insertHelper->content = $content;
                $insertHelper->bannerHtml = static::renderRelated($posts[2]);
                $insertHelper->adaptiveIndex = 2/3;
                $content = $insertHelper->run();
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
        include 'views/similarArticles.html';
        return ob_get_clean();
    }
}