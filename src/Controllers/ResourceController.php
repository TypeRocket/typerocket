<?php

namespace TypeRocket\Controllers;

abstract class ResourceController extends Controller
{
    abstract function edit( $id );

    abstract function add();

    abstract function show( $id );

    abstract function index();

    abstract function delete($id);

}