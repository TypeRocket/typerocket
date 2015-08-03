<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

abstract class Responder
{
    abstract public function respond( $id );

    /**
     * Run the Kernel
     *
     * A class XKernel to override the Kernel but it should extend the
     * Kernel.
     *
     * @param Request $request
     * @param Response $response
     * @param string $type
     */
    public function runKernel(Request $request, Response $response, $type = 'hookGlobal')
    {
        $XKernel = "\\TypeRocket\\Http\\XKernel";

        if ( class_exists( $XKernel ) ) {
            new $XKernel( $request, $response, $type);
        } else {
            new Kernel($request, $response, $type);
        }
    }
}