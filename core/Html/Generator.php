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

    function newElement( $tag, array $attributes = array(), $text = '' )
    {
        $this->tag = new Tag( $tag, $attributes, $text );

        return $this;
    }

    function newLink( $text = '', $url = '#', array $attributes = array() )
    {

        $attributes = array_merge( array( 'href' => $url ), $attributes );

        $this->tag = new Tag( 'a', $attributes, $text );

        return $this;
    }

    function newImage( $src = '', array $attributes = array() )
    {

        $attributes = array_merge( array( 'src' => $src ), $attributes );

        $this->tag = new Tag( 'img', $attributes );

        return $this;
    }

    function newInput( $type, $name, $value, array $attributes = array() )
    {

        $attributes = array_merge( array( 'type' => $type, 'name' => $name, 'value' => $value), $attributes );

        $this->tag = new Tag( 'input', $attributes );

        return $this;
    }

    function getTag()
    {
        return $this->tag;
    }

    function setTag(Tag $tag)
    {
        $this->tag = $tag;

        return $this;
    }

    function getString()
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
    function appendInside( $tag, array $attributes = array(), $text = '' )
    {

        switch ($tag) {
            case $tag instanceof Tag :
                $this->tag->appendInnerTag( $tag );
                break;
            case $tag instanceof Generator :
                $this->tag->appendInnerTag( $this->tag );
                break;
            case is_string( $tag ) :
                $this->tag->appendInnerTag( new Tag( $tag, $attributes, $text ) );
                break;
        }

        return $this;

    }

    /**
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     *
     * @return $this
     */
    function prependInside( $tag, array $attributes = array(), $text = '' )
    {

        switch ($tag) {
            case $tag instanceof Tag :
                $this->tag->prependInnerTag( $tag );
                break;
            case $tag instanceof Generator :
                $this->tag->prependInnerTag( $this->tag );
                break;
            case is_string( $tag ) :
                $this->tag->prependInnerTag( new Tag( $tag, $attributes, $text ) );
                break;
        }

        return $this;

    }

}
