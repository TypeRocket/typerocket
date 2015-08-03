<?php
namespace TypeRocket\Http\Responders;

/**
 * Class Hook
 *
 * Used by core to hook into WordPress API
 *
 * @package TypeRocket\Http\Responders
 */
class Hook {

    static public function posts($id) {
        $responder = new PostsResponder();
        $responder->respond($id);
    }

    static public function comments($id) {
        $responder = new CommentsResponder();
        $responder->respond($id);
    }

    static public function users($id) {
        $responder = new UsersResponder();
        $responder->respond($id);
    }

}