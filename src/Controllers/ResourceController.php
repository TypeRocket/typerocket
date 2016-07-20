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
     * Delete
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function delete($id);

}