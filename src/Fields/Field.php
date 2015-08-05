<?php
namespace TypeRocket\Fields;

use \TypeRocket\Form,
    \TypeRocket\Validate,
    \TypeRocket\Sanitize;

abstract class Field
{

    private $name = null;
    private $type = null;
    private $attr = array();

    private $itemId = null;
    private $resource = null;
    /** @var Form */
    private $form = null;

    private $prefix = null;
    private $group = null;
    private $sub = null;
    private $brackets = null;

    private $label = false;
    private $settings = array();
    private $populate = true;

    /**
     * When instancing a Field use reflection to connect the Form
     *
     * @param string $name the name of the field
     * @param array $attr the html attributes
     * @param array $settings the settings of the field
     * @param bool|true $label show the label
     *
     * @internal A Form must be passed for the field to work
     */
    public function __construct( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $args = func_get_args();
        $this->init();
        $setup = new \ReflectionMethod( $this, 'setup' );
        $setup->setAccessible(true);

        $args = $this->assignAutoArgs($args);

        if ($this instanceof ScriptField) {
            $this->enqueueScripts();
        }

        $setup->invokeArgs( $this, $args );
        $setup->setAccessible(false);
    }

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    /**
     * Get Field object as string
     *
     * @return string
     */
    public function __toString()
    {
        $form = $this->getForm();
        if($form instanceof Form) {
            $string = $this->getForm()->getFromFieldString($this);
        } else {
            $string = $this->getString();
        }

        return $string;
    }

    /**
     * Require Form
     *
     * @param $args
     *
     * @return mixed
     */
    private function assignAutoArgs($args) {
        foreach ($args as $key => $arg) {
            if ($arg instanceof Form) {
                $this->configureToForm( $arg );
                unset( $args[$key] );
                return $args;
            }
        }

        die('TypeRocket: A field does not have a From connected to it.');
    }

    /**
     * Invoked by Reflection in constructor
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return $this
     */
    private function setup( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $this->settings = $settings;
        $this->label    = $label;
        $this->attr     = $attr;
        $this->setName( $name );

        if (empty( $settings['label'] )) {
            $this->settings['label'] = $name;
        }

        return $this;

    }

    /**
     * Init is normally used to setup initial configuration like a
     * constructor does.
     *
     * @return mixed
     */
    abstract protected function init();

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
        $this->itemId = $form->getItemId();
        $this->resource = $form->getResource();
        $this->setPopulate( $form->getPopulate() );
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

    /**
     * Set Group into bracket syntax
     *
     * @param $group
     *
     * @return $this
     */
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

    /**
     * Get Group
     *
     * @return null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set Sub Group into bracket syntax
     *
     * @param $sub
     *
     * @return $this
     */
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

    /**
     * Get Sub Group
     *
     * @return null
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * Set whether to populate Field from database. If set to false fields will
     * always be left empty and with their default values.
     *
     * @param $populate
     *
     * @return $this
     */
    public function setPopulate( $populate )
    {
        $this->populate = (bool) $populate;

        return $this;
    }

    /**
     * Get populate
     *
     * @return bool
     */
    public function getPopulate()
    {
        return $this->populate;
    }

    /**
     * Set Attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes( array $attributes )
    {
        $this->attr = $attributes;

        return $this;
    }

    /**
     * Get Attribute by key
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attr;
    }

    /**
     * Set Attribute by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setAttribute( $key, $value )
    {
        $this->attr[(string) $key] = $value;

        return $this;
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

    /**
     * Remove Attribute by key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeAttribute( $key )
    {

        if (array_key_exists( $key, $this->attr )) {
            unset( $this->attr[$key] );
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

    /**
     * Generate the string needed for the html name attribute. This
     * does not set the name attribute.
     *
     * @return string
     */
    public function getNameAttributeString()
    {

        if (empty( $this->prefix )) {
            $this->setPrefix();
        }

        $this->brackets = $this->getBrackets();

        return $this->prefix . $this->brackets;
    }

    /**
     * Get Resource
     *
     * @return null
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get Item ID
     *
     * @return null
     */
    public function getItemId()
    {
        return $this->itemId;
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

    /**
     * Get Type
     *
     * @return null
     */
    public function getType()
    {
        return $this->type;
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

    /**
     * Set Settings
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings( $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get Settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set Setting
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setSetting( $key, $value )
    {
        $this->settings[$key] = $value;

        return $this;
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

    /**
     * Remove Setting by key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeSetting( $key )
    {

        if (array_key_exists( $key, $this->settings )) {
            unset( $this->settings[$key] );
        }

        return $this;
    }

    /**
     * Render Setting
     *
     * By setting render to 'raw' the form will not add any special html wrappers.
     * You have more control of the design when render is set to raw.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setRenderSetting( $value )
    {

        $this->settings['render'] = $value;

        return $this;
    }

    /**
     * Get render mode
     *
     * @return null
     */
    public function getRenderSetting()
    {
        if ( ! array_key_exists( 'render', $this->settings )) {
            return null;
        }

        return $this->settings['render'];
    }

    /**
     * Label Text
     *
     * Set the label text to be used
     *
     * @param string $value
     *
     * @return $this
     */
    public function setLabel( $value )
    {

        $this->settings['label'] = $value;

        return $this;
    }

    /**
     * Get Label
     *
     * This is not the label text but the label setting. Whether it
     * should be displayed.
     *
     * @return bool
     */
    public function getLabel()
    {
        if ( ! array_key_exists( 'label', $this->settings )) {
            return null;
        }

        return $this->settings['label'];
    }

    /**
     * Set whether label should be displayed
     *
     * @param $label
     *
     * @return $this
     */
    public function setLabelOption( $label )
    {
        $this->label = (bool) $label;

        return $this;
    }

    /**
     * Get Label Option
     *
     * This is not the label text but the label setting. Whether it
     * should be displayed.
     *
     * @return bool
     */
    function getLabelOption()
    {
        return $this->label;
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

        return $this;
    }

    /**
     * Get the prefix
     *
     * @return null
     */
    public function getPrefix()
    {
        return $this->prefix;
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

        $value = $this->form->getModel()->getFieldValue($this);

        return $value;
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
     * Configure in all concrete Field classes
     *
     * @return string
     */
    abstract public function getString();

}