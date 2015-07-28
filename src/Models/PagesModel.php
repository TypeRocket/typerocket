<?php
namespace TypeRocket\Models;

class PagesModel extends PostTypesModel
{
    protected $guard = array(
        'post_type'
    );
}
