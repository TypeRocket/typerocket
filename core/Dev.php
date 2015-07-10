<?php

namespace TypeRocket;

class Dev {

    function getFieldHelpFunction(Fields\Field $field) {

        $brackets = $field->getBrackets();
        $controller = $field->getController();
        $function = "tr_{$controller}_field('{$brackets}');";

        if($field->builtin) {
            $function = 'Builtin as: ' . $field->name;
        }

        return $function;
    }

}
