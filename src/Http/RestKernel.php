<?php
namespace TypeRocket\Http;

class RestKernel extends Kernel
{
    protected $middleware = array(
        '\\TypeRocket\\Http\\Middleware\\AuthRead',
        '\\TypeRocket\\Http\\Middleware\\ValidateCsrf'
    );

}