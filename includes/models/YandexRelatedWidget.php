<?php

class YandexRelatedWidget
{
    public static function insert_related($content) {
        global $post, $wpdb;
        $thePostID = $post->ID;

        $countPrs = substr_count($content, '<p');
  
        $query = "SELECT wp_posts.post_title as title, wp_posts.post_name as url, wp_posts.id as id FROM wp_posts INNER JOIN wp_yandex_related ON wp_posts.id = wp_yandex_related.related_article_id WHERE wp_yandex_related.article_id = $thePostID ORDER BY wp_yandex_related.related_article_id DESC";

        $posts = $wpdb->get_results($query);
        $posts = array_chunk($posts, 3);

        $firstblock = (get_option('firstblock') == 'on');
        $secondblock = (get_option('secondblock') == 'on');
        $thirdblock = (get_option('thirdblock') == 'on');

        $postsIndex = 0;

        if ( ! is_admin() ) {
            $insertHelper = new InsertHelper();

            if(isset($posts[$postsIndex]) && $firstblock) {
                $insertHelper->content = $content;
                $insertHelper->bannerHtml = static::renderRelated($posts[$postsIndex]);
                $index = eval('return ' . get_option('firstblockindex') . ';');
                if(is_integer($index)) {
                    $insertHelper->paragraphId = $index;
                } else {
                    $insertHelper->adaptiveIndex = $index;
                }
                $content = $insertHelper->run();
                $postsIndex++;
            }
            
            if(isset($posts[$postsIndex]) && $secondblock) {
                $insertHelper->content = $content;
                $insertHelper->bannerHtml = static::renderRelated($posts[$postsIndex]);
                $index = eval('return ' . get_option('secondblockindex') . ';');
                if(is_integer($index)) {
                    $insertHelper->paragraphId = $index;
                } else {
                    $insertHelper->adaptiveIndex = $index;
                }
                $content = $insertHelper->run();
                $postsIndex++;
            }

            if(isset($posts[$postsIndex]) && $thirdblock) {
                $insertHelper->content = $content;
                $insertHelper->bannerHtml = static::renderRelated($posts[$postsIndex]);
                $index = eval('return ' . get_option('thirdblockindex') . ';');
                if(is_integer($index)) {
                    $insertHelper->paragraphId = $index;
                } else {
                    $insertHelper->adaptiveIndex = $index;
                }
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