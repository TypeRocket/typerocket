<?php

namespace TypeRocket\Controllers;

abstract class ResourceController extends Controller
{

    /**
     * The edit page
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function edit( $id );

    /**
     * The add page
     *
     * @return mixed
     */
    abstract function add();

    /**
     * The show page
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function show( $id );

    /**
     * The index page
     *
     * @return mixed
     */
    abstract function index();

    /**
     * The delete page
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function delete($id);

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function destroy($id);


}