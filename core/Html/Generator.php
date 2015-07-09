<?php
namespace TypeRocket\Html;

class Generator
{

    /** @var Tag */
    public $tag;

    function element( $tag, array $attributes = array(), $text = '' )
    {
        $this->tag = new Tag( $tag, $attributes, $text );
    }

    function link( $text = '', $url = '#', array $attributes = array() )
    {

        $attributes = array_merge( array( 'href' => $url ), $attributes );

        $this->tag = new Tag( 'a', $attributes, $text );
    }

    function image( $src = '', array $attributes = array() )
    {

        $attributes = array_merge( array( 'src' => $src ), $attributes );

        $this->tag = new Tag( 'img', $attributes );
    }

    function getTag()
    {
        return $this->tag;
    }

    function getString()
    {
        return $this->tag->getString();
    }

    /**
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     */
    function append( $tag, array $attributes = array(), $text = '' )
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

    }

    /**
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     */
    function prepend( $tag, array $attributes = array(), $text = '' )
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

    }

}
