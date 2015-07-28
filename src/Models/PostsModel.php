<?php
namespace TypeRocket\Models;

class PostsModel extends PostTypesModel
{
    protected $guard = array(
        'post_type'
    );
}
