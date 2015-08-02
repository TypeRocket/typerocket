<?php
namespace TypeRocket\Http;

class RestKernel extends Kernel
{
    protected $middleware = array(
        '\\TypeRocket\\Http\\Middleware\\AuthAdmin',
        '\\TypeRocket\\Http\\Middleware\\ValidateCsrf'
    );

}