<?php
namespace TypeRocket\Html;

class Generator
{

    /** @var Tag */
    public $tag;

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    public function __toString()
    {
        return $this->getString();
    }

    public function newElement( $tag, array $attributes = array(), $text = '' )
    {
        $this->tag = new Tag( $tag, $attributes, $text );

        return $this;
    }

    public function newLink( $text = '', $url = '#', array $attributes = array() )
    {

        $attributes = array_merge( array( 'href' => $url ), $attributes );

        $this->tag = new Tag( 'a', $attributes, $text );

        return $this;
    }

    public function newImage( $src = '', array $attributes = array() )
    {

        $attributes = array_merge( array( 'src' => $src ), $attributes );

        $this->tag = new Tag( 'img', $attributes );

        return $this;
    }

    public function newInput( $type, $name, $value, array $attributes = array() )
    {

        $defaults = array( 'type' => $type, 'name' => $name, 'value' => $value );

        if (is_null( $name )) {
            unset( $defaults['name'] );
        }

        if (is_null( $value )) {
            unset( $defaults['value'] );
        }

        $attributes = array_merge( $defaults, $attributes );

        $this->tag = new Tag( 'input', $attributes );

        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag( Tag $tag )
    {
        $this->tag = $tag;

        return $this;
    }

    public function getString()
    {
        return $this->tag->getString();
    }

    /**
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     *
     * @return $this
     */
    public function appendInside( $tag, array $attributes = array(), $text = '' )
    {

        if ($tag instanceof Generator) {
            $tag = $tag->tag;
        } elseif (is_string( $tag )) {
            $tag = new Tag( $tag, $attributes, $text );
        }

        $this->tag->appendInnerTag( $tag );

        return $this;

    }

    /**
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     *
     * @return $this
     */
    public function prependInside( $tag, array $attributes = array(), $text = '' )
    {

        if ($tag instanceof Generator) {
            $tag = $tag->tag;
        } elseif (is_string( $tag )) {
            $tag = new Tag( $tag, $attributes, $text );
        }

        $this->tag->prependInnerTag( $tag );

        return $this;

    }

}
