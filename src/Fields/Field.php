<?php
namespace TypeRocket\Fields;

use \TypeRocket\Form as Form,
    \TypeRocket\Validate as Validate,
    \TypeRocket\Sanitize as Sanitize,
    \TypeRocket\GetValue as GetValue;

abstract class Field
{

    private $name = null;
    private $type = null;
    private $attr = array();

    private $item_id = null;
    private $controller = null;
    /** @var Form */
    private $form = null;

    private $prefix = null;
    private $group = null;
    private $sub = null;
    private $brackets = null;

    private $label = false;
    private $settings = array();
    private $builtin = false;
    private $populate = true;

    /**
     * When instancing a Field use reflection to connect the Form
     *
     * @param string $name the name of the field
     * @param array $attr the html attributes
     * @param array $settings the settings of the field
     * @param bool|true $label show the label
     */
    function __construct( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $args = func_get_args();
        $this->init();
        $setup = new \ReflectionMethod( $this, 'setup' );

        foreach ($args as $key => $arg) {
            if ($arg instanceof Form) {
                $this->configureToForm( $arg );
                unset( $args[$key] );
            }
        }

        if ($this instanceof FieldScript) {
            $this->enqueueScripts();
        }

        $setup->invokeArgs( $this, $args );
    }

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    public function init()
    {
        return $this;
    }

    /**
     * Setup to use with a Form.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function configureToForm( Form $form )
    {
        $this->setGroup( $form->getGroup() );
        $this->setSub( $form->getSub() );
        $this->setItemId( $form->getItemId() );
        $this->setController( $form->getController() );
        $this->setPopulate( $form->getPopulate() );
        $this->setForm( $form ); // do not pass by reference

        return $this;
    }

    /**
     * Set the From for the field not as a reference. From is
     * cloned to help eliminate errors.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function setForm( Form $form )
    {
        $this->form = clone $form;

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    public function setGroup( $group )
    {
        $this->group = null;

        if (Validate::bracket( $group )) {
            $this->group = $group;
        } elseif (is_string( $group )) {
            $this->group = "[{$group}]";
        }

        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setSub( $sub )
    {
        $this->sub = null;

        if (Validate::bracket( $sub )) {
            $this->sub = $sub;
        } elseif (is_string( $sub )) {
            $this->sub = "[{$sub}]";
        }

        return $this;
    }

    public function setAttributes( array $attributes )
    {
        $this->attr = $attributes;

        return $this;
    }

    public function setPopulate( $populate )
    {
        $this->populate = (bool) $populate;

        return $this;
    }

    function getPopulate()
    {
        return $this->populate;
    }

    public function setBuiltin( $in )
    {
        $this->builtin = (bool) $in;

        return $this;
    }

    function getBuiltin()
    {
        return $this->builtin;
    }

    public function setLabel( $label )
    {
        $this->label = (bool) $label;

        return $this;
    }

    function getLabel()
    {
        return $this->label;
    }

    public function getAttributes()
    {
        return $this->attr;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return null
     */
    public function getAttribute( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->attr )) {
            return $default;
        }

        return $this->attr[$key];
    }

    public function setAttribute( $key, $value )
    {
        $this->attr[(string) $key] = $value;

        return $this;
    }

    public function removeAttribute( $key )
    {

        if (array_key_exists( $key, $this->attr )) {
            unset( $this->attr[$key] );
        }

        return $this;
    }

    public function removeSetting( $key )
    {

        if (array_key_exists( $key, $this->settings )) {
            unset( $this->settings[$key] );
        }

        return $this;
    }

    public function setItemId( $item_id )
    {
        if (isset( $item_id )) {
            $this->item_id = (int) $item_id;
        }

        return $this;
    }

    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Set the type of Field. This is not always the input type. For
     * example in custom fields. Text Field is the only that does.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType( $type )
    {

        $this->type = (string) $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setController( $controller )
    {
        if (isset( $controller )) {
            $this->controller = $controller;
        }

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }


    /**
     * Set name of field. Not the same as the html name attribute.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName( $name )
    {
        $this->name = Sanitize::underscore( $name );

        return $this;
    }

    /**
     * Get name of field. Not the same as the html name attribute.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setSetting( $key, $value )
    {
        $this->settings[$key] = $value;

        return $this;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get Setting
     *
     * @param $key
     * @param null $default default value to return if none
     *
     * @return null
     */
    public function getSetting( $key, $default = null )
    {

        if ( ! array_key_exists( $key, $this->settings )) {
            return $default;
        }

        return $this->settings[$key];
    }

    public function getRender()
    {
        if ( ! array_key_exists( 'render', $this->settings )) {
            return null;
        }

        return $this->settings['render'];
    }

    public function setRender( $value )
    {

        $this->settings['render'] = $value;

        return $this;
    }

    /**
     * Set the prefix that goes before the brackets when setting
     * the initial name attribute.
     *
     * @param string $prefix set to tr by default
     *
     * @return $this
     */
    public function setPrefix( $prefix = 'tr' )
    {

        $this->prefix = (string) $prefix;

        if ($this->builtin == true) {
            $this->prefix = '_tr_builtin_data';
        }

        return $this;
    }

    /**
     * Append a string to an attribute
     *
     * @param string $key the attribute if set
     * @param string $text the string to append
     * @param string $separator separate stings by this
     *
     * @return $this
     */
    public function appendStringToAttribute( $key, $text, $separator = ' ' )
    {

        if (array_key_exists( $key, $this->attr )) {
            $text = $this->attr[$key] . $separator . (string) $text;
        }

        $this->attr[$key] = $text;

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Generate the string needed for the html name attribute. This
     * does not set the name attribute.
     *
     * @return string
     */
    public function generateNameAttributeString()
    {

        if (empty( $this->prefix )) {
            $this->setPrefix();
        }

        if (empty( $this->brackets )) {
            $this->setBrackets( $this->getBrackets() );
        }

        return $this->prefix . $this->brackets;
    }

    /**
     * Setup the field
     *
     * @param string $name the name of the field
     * @param array $attr attributes of the html element
     * @param array $settings settings for the field
     *
     * @param bool $label show the label
     *
     * @return $this
     */
    public function setup( $name, array $attr = array(), array $settings = array(), $label = true )
    {

        do_action( 'tr_start_setup_field', $this, $name, $attr, $settings, $label );

        $this->settings = $settings;
        $this->label    = $label;

        if (isset( $settings['builtin'] ) && $settings['builtin'] == true) {
            $this->builtin = true;
        }

        if (array_key_exists( 'class', $attr )) {
            $attr['class'] .= ' ' . $this->attr['class'];
        }

        $this->attr['class'] = apply_filters( 'tr_field_class_attribute_filter', $this->attr['class'], $this );

        if ( ! $this->attr['class']) {
            unset( $this->attr['class'] );
        }

        $this->attr = array_merge( $this->attr, $attr );
        $this->setName( $name );
        $this->attr['name'] = $this->generateNameAttributeString();

        if (empty( $settings['label'] )) {
            $this->settings['label'] = $name;
        }

        do_action( 'tr_end_setup_field', $this, $name, $attr, $settings, $label );

        return $this;

    }


    /**
     * Get the value from the database
     *
     * @return null|string return the fields value
     */
    public function getValue()
    {

        if ($this->populate == false) {
            return null;
        }

        $getter = new GetValue();

        return $getter->getFromField( $this );
    }


    /**
     * Get bracket syntax used to name the input and get the
     * value from the database using the GetValue class.
     *
     * @return string format [group][name][sub]
     */
    public function getBrackets()
    {
        return "{$this->group}[{$this->name}]{$this->sub}";
    }


    /**
     * Set the brackets
     *
     * @param string $brackets format [group][name][sub]
     *
     * @return $this
     */
    public function setBrackets( $brackets )
    {
        if (Validate::bracket( $brackets )) {
            $this->brackets = $brackets;
        }

        return $this;
    }


    /**
     * Configure in all concrete Field classes
     *
     * @return string
     */
    public function getString()
    {
        return '';
    }

}