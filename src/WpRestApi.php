<?php

namespace TypeRocket;

class WpRestApi
{

    public static function search(  \WP_REST_Request $request ) {
        $func = function( $search, &$wp_query )
        {
            global $wpdb;
            if ( empty( $search ) )
                return $search; // skip processing - no search term in query
            $q = $wp_query->query_vars;
            $n = ! empty( $q['exact'] ) ? '' : '%';
            $search =
            $searchand = '';
            foreach ( (array) $q['search_terms'] as $term ) {
                $term = esc_sql( like_escape( $term ) );
                $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
                $searchand = ' AND ';
            }
            if ( ! empty( $search ) ) {
                $search = " AND ({$search}) ";
                if ( ! is_user_logged_in() )
                    $search .= " AND ($wpdb->posts.post_password = '') ";
            }
            return $search;
        };

        $limit = 10;
        $params = $request->get_params();
        $results = null;

        if( array_key_exists('taxonomy', $params) ) {
            $taxonomy = $params['taxonomy'];
            $terms = get_terms( [
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'search' =>  $params['s'],
                'number' => $limit

            ] );

            $results = $terms;
        } else {
            add_filter( 'posts_search', $func, 500, 2 );
            $query = new \WP_Query( [
                'post_type' => $params['post_type'],
                's' => $params['s'],
                'posts_per_page' => $limit
            ] );

            if ( ! empty( $query->posts ) ) {
                $results =  $query->posts;
            }
        }

        return $results;
    }

}