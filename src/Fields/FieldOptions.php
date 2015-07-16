<?php
namespace TypeRocket\Fields;


interface FieldOptions
{
    public function setOption($key, $value);
    public function setOptions($options);
    public function getOption($key, $default = null);
    public function getOptions();
    public function removeOption($key);
}