<?php

namespace TypeRocket;

class WpRestApi
{

    /**
     * Get search results list
     *
     * @param \WP_REST_Request $request
     *
     * @return array|int|null|\WP_Error
     */
    public static function search(  \WP_REST_Request $request ) {
        $limit = 10;
        $params = $request->get_params();
        $results = null;

        if( array_key_exists('taxonomy', $params) ) {
            $results = get_terms( [
                'taxonomy' => $params['taxonomy'],
                'hide_empty' => false,
                'search' =>  $params['s'],
                'number' => $limit
            ] );
        } else {
            add_filter( 'posts_search', '\TypeRocket\WpRestApi::posts_search', 500, 2 );
            $query = new \WP_Query( [
                'post_type' => $params['post_type'],
                's' => $params['s'],
                'post_status' => ['publish', 'pending', 'draft', 'future'],
                'posts_per_page' => $limit
            ] );

            if ( ! empty( $query->posts ) ) {
                $results =  $query->posts;
            }
        }

        return $results;
    }

    /**
     * Posts search hook
     *
     * @param $search
     * @param $wp_query
     *
     * @return string
     */
    public static function posts_search( $search, &$wp_query )
    {
        global $wpdb;
        if ( ! empty( $search ) ) {
            $q = $wp_query->query_vars;
            $search = $searchand = '';
            foreach ( (array) $q['search_terms'] as $term ) {
                $term = esc_sql( $wpdb->esc_like( $term ) );
                $search .= "{$searchand}({$wpdb->posts}.post_title LIKE '%{$term}%')";
                $searchand = ' AND ';
            }
            if ( ! empty( $search ) ) {
                $search = " AND ({$search}) ";
                if ( ! is_user_logged_in() ) {
                    $search .= " AND ({$wpdb->posts}.post_password = '') ";
                }
            }
        }

        return $search;
    }

}
