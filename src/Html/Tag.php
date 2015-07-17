<?php
namespace TypeRocket\Html;

class Tag
{

    private $tag;
    private $attributes;
    private $text;

    /** @var  TagCollection */
    private $innerHtmlTags;
    private $specialTags = array( 'img' => true, 'input' => true, 'hr' => true, 'br' => true );
    private $specialTag = false;


    /**
     * Html constructor.
     *
     * @param $tag
     * @param $attributes
     * @param string $text
     */
    public function __construct( $tag, $attributes, $text = '')
    {
        $this->setTag( $tag );
        $this->setAttributes( $attributes );
        $this->setText( $text );
        $this->setInnerHtmlTags( new TagCollection() );

        if( array_key_exists($this->tag, $this->specialTags) ) {
            $this->specialTag = true;
        }

        return $this;
    }

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

    public function getTag()
    {
        return $this->tag;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getSpecialTag()
    {
        return $this->specialTag;
    }

    public function getAttributeValue( $attribute )
    {
        $value = null;

        if (array_key_exists( $attribute, $this->attributes )) {
            $value = $this->attributes[$attribute];
        }

        return $value;
    }

    public function getInnerHtmlTags()
    {
        $this->innerHtmlTags;
    }

    public function setText( $text )
    {
        $this->text = (string) $text;

        return $this;
    }

    public function setTag( $tag )
    {
        if (is_string( $tag )) {
            $this->tag = $tag;
        }

        return $this;
    }

    public function setAttributes( array $attributes )
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setAttribute( $attribute, $value )
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    public function updateAttributes( array $attributes )
    {
        $this->attributes = array_merge( $this->attributes, $attributes );

        return $this;
    }

    public function removeAttribute( $attribute )
    {
        if (array_key_exists( $attribute, $this->attributes )) {
            unset( $this->attributes[$attribute] );
        }

        return $this;
    }

    public function appendInnerTag( Tag $tag )
    {
        $this->innerHtmlTags->append( $tag );

        return $this;
    }

    public function prependInnerTag( Tag $tag )
    {
        $this->innerHtmlTags->prepend( $tag );

        return $this;
    }

    /**
     * @param TagCollection $collection
     *
     * @return $this
     */
    public function setInnerHtmlTags( TagCollection $collection )
    {
        $this->innerHtmlTags = $collection;

        return $this;
    }

    public function getString() {

        $openTag = $this->getStringOpenTag();
        $closeTag = $this->getStringCloseTag();
        $innerHtmlWithTags = $this->getStringInnerHtmlWithTags();

        return $openTag.$innerHtmlWithTags.$closeTag;

    }

    public function getStringOpenTag() {
        $openTag = "<{$this->tag}";

        foreach($this->attributes as $attribute => $value) {
            $openTag .= " {$attribute}=\"{$value}\"";
        }

        if( $this->specialTag ) {
            $openTag .= " />";
        } else {
            $openTag .= ">";
        }

        return $openTag;
    }

    public function getStringCloseTag() {
        $closeTag = '';

        if( ! $this->specialTag ) {
            $closeTag = "</{$this->tag}>";
        }

        return $closeTag;
    }

    public function getStringInnerHtmlWithTags() {
        $innerHtmlTags = $innerHtml ='';

        if( ! $this->specialTag ) {
            $innerHtml = $this->text;
            foreach($this->innerHtmlTags as $tag) {
                $innerHtmlTags .= $tag->getString();
            }
        }

        return $innerHtml.$innerHtmlTags;
    }

}
