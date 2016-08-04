<?php

namespace TypeRocket\Http\Rewrites;

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
            add_filter( 'posts_search', '\TypeRocket\Http\Rewrites\WpRestApi::posts_search', 500, 2 );
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

    /**
     * Decide is a user can access the search API
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed|void
     */
    public static function permission( \WP_REST_Request $request )
    {
        $permissions = false;
        $logged_in = wp_validate_auth_cookie( $_COOKIE[LOGGED_IN_COOKIE], 'logged_in' );
        if( $logged_in ) {
            list($username, $time, $token) = explode('|',$_COOKIE[LOGGED_IN_COOKIE], 3);
            $user = get_user_by('login', $username);
            $permissions = user_can( $user, 'edit_others_posts' );
        }
        return apply_filters('tr_rest_search', $permissions);
    }

}
