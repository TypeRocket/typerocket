<?php
namespace TypeRocket\Http;

class RestKernel extends Kernel
{
    protected $middleware = array(
        '\\TypeRocket\\Http\\Middleware\\ValidateCsrf',
        '\\TypeRocket\\Http\\Middleware\\AuthRead',
    );

}