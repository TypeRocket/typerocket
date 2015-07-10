<?php

namespace TypeRocket;

class Dev {

    function getFieldHelpFunction(Fields\Field $field) {

        $brackets = $field->getBrackets();
        $controller = $field->getController();
        $function = "tr_{$controller}_field('{$brackets}');";

        if($field->getBuiltin()) {
            $function = 'Builtin as: ' . $field->getName();
        }

        return $function;
    }

}
