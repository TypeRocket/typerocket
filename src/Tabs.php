<?php
namespace TypeRocket;

class Tabs
{

    private $tabs = array();
    private $sidebar = null;

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    /**
     * Gets the help tabs registered for the screen.
     *
     * @since 3.4.0
     *
     * @return array Help tabs with arguments.
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * Gets the arguments for a help tab.
     *
     * @since 3.4.0
     *
     * @param string $id Help Tab ID.
     *
     * @return array Help tab arguments.
     */
    public function getTab( $id )
    {
        if ( ! isset( $this->tabs[$id] )) {
            return null;
        }

        return $this->tabs[$id];
    }

    /**
     * Add a help tab to the contextual help for the screen.
     * Call this on the load-$pagenow hook for the relevant screen.
     *
     * @since 3.3.0
     *
     * @param array|string $settings
     * - string   - title    - Title for the tab.
     * - string   - id       - Tab ID. Must be HTML-safe.
     * - string   - content  - Help tab content in plain text or HTML. Optional.
     * - callback - callback - A callback to generate the tab content. Optional.
     *
     * @param array $content the content if settings is not an array
     *
     * @return $this
     */
    public function addTab( $settings, $content = null )
    {

        if( ! is_array($settings)) {
            $args = func_get_args();
            $settings = array();
            $settings['id'] = Sanitize::underscore($args[0]);
            $settings['title'] = $args[0];
            $settings['content'] = $args[1];
        }

        $this->addTabFromArray($settings);
        return $this;
    }

    /**
     * Add tabs using array format
     *
     * @param $settings
     *
     * @return $this
     */
    private function addTabFromArray($settings) {
        $defaults = array(
            'title'    => false,
            'id'       => false,
            'content'  => '',
            'callback' => false,
            'url'      => false
        );
        $settings     = wp_parse_args( $settings, $defaults );

        $settings['id'] = sanitize_html_class( $settings['id'] );

        // Ensure we have an ID and title.
        if ( ! $settings['id'] || ! $settings['title']) {
            echo "TypeRocket: Tab needs ID and Title";
            die();
        }

        // Allows for overriding an existing tab with that ID.
        $this->tabs[$settings['id']] = $settings;

        return $this;
    }

    /**
     * Removes a help tab from the contextual help for the screen.
     *
     * @since 3.3.0
     *
     * @param string $id The help tab ID.
     *
     * @return $this
     */
    public function removeTab( $id )
    {
        unset( $this->tabs[$id] );

        return $this;
    }

    /**
     * Removes all help tabs from the contextual help for the screen.
     *
     * @since 3.3.0
     */
    public function removeTabs()
    {
        $this->tabs = array();

        return $this;
    }

    /**
     * Gets the content from a contextual help sidebar.
     *
     * @since 3.4.0
     *
     * @return string Contents of the help sidebar.
     */
    public function getSidebar()
    {
        return $this->sidebar;
    }

    /**
     * Add a sidebar to the contextual help for the screen.
     * Call this in template files after admin.php is loaded and before admin-header.php is loaded to add a sidebar to
     * the contextual help.
     *
     * @since 3.3.0
     *
     * @param string $content Sidebar content in plain text or HTML.
     *
     * @return $this
     */
    public function setSidebar( $content )
    {
        $this->sidebar = $content;

        return $this;
    }

    /**
     * Render the screen's help section.
     *
     * This will trigger the deprecated filters for backwards compatibility.
     *
     * @since 3.3.0
     *
     * @param string $style meta|default
     *
     * @return $this
     */
    public function render( $style = null )
    {
        switch ($style) {
            case 'box' :
                $this->leftBoxedStyleTabs();
                break;
            default :
                $this->topStyleTabs();
                break;
        }

        return $this;

    }

    /**
     * Tabs at the top
     */
    private function topStyleTabs()
    {
        // Default help only if there is no old-style block of text and no new-style help tabs.
        $help_sidebar = $this->getSidebar();

        // Time to render!
        ?>

        <div class="tr-tabbed-top cf">
            <div class="tabbed-sections">
                <ul class="tr-tabs alignleft">
                    <?php
                    $class = ' class="active"';
                    $tabs  = $this->getTabs();
                    foreach ($tabs as $tab) :
                        $link_id = "tab-link-{$tab['id']}";
                        $panel_id = ( ! empty( $tab['url'] ) ) ? $tab['url'] : "#tab-panel-{$tab['id']}";
                        ?>
                        <li id="<?php echo esc_attr( $link_id ); ?>"<?php echo $class; ?>>
                            <a href="<?php echo esc_url( "$panel_id" ); ?>">
                                <?php echo esc_html( $tab['title'] ); ?>
                            </a>
                        </li>
                        <?php
                        $class   = '';
                    endforeach;
                    ?>
                </ul>
            </div>

            <?php if ($help_sidebar) : ?>
                <div class="tabbed-sidebar">
                    <?php echo $help_sidebar; ?>
                </div>
            <?php endif; ?>

            <div class="tr-sections">
                <?php
                $classes = 'tab-section active';
                foreach ($tabs as $tab):
                    $panel_id = "tab-panel-{$tab['id']}";
                    ?>

                    <div id="<?php echo esc_attr( $panel_id ); ?>" class="<?php echo $classes; ?>">
                        <?php
                        // Print tab content.
                        echo (string) $tab['content'];

                        // If it exists, fire tab callback.
                        if ( ! empty( $tab['callback'] )) {
                            call_user_func_array( $tab['callback'], array( $this, $tab ) );
                        }
                        ?>
                    </div>
                    <?php
                    $classes  = 'tab-section';
                endforeach;
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Tabs boxes in like with help tabs
     */
    private function leftBoxedStyleTabs()
    {
        // Default help only if there is no old-style block of text and no new-style help tabs.
        $help_sidebar = $this->getSidebar();

        $help_class = '';
        if ( ! $help_sidebar) :
            $help_class .= ' no-sidebar';
        endif;

        // Time to render!
        ?>
        <div class="tr-tabbed-box metabox-prefs">

            <div class="tr-contextual-help-wrap <?php echo esc_attr( $help_class ); ?> cf">
                <div class="tr-contextual-help-back"></div>
                <div class="tr-contextual-help-columns">
                    <div class="contextual-help-tabs">
                        <ul>
                            <?php
                            $class = ' class="active"';
                            $tabs  = $this->getTabs();
                            foreach ($tabs as $tab) :
                                $link_id = "tab-link-{$tab['id']}";
                                $panel_id = ( ! empty( $tab['url'] ) ) ? $tab['url'] : "#tab-panel-{$tab['id']}";
                                ?>
                                <li id="<?php echo esc_attr( $link_id ); ?>"<?php echo $class; ?>>
                                    <a href="<?php echo esc_url( "$panel_id" ); ?>">
                                        <?php echo esc_html( $tab['title'] ); ?>
                                    </a>
                                </li>
                                <?php
                                $class   = '';
                            endforeach;
                            ?>
                        </ul>
                    </div>

                    <?php if ($help_sidebar) : ?>
                        <div class="contextual-help-sidebar">
                            <?php echo $help_sidebar; ?>
                        </div>
                    <?php endif; ?>

                    <div class="contextual-help-tabs-wrap">
                        <?php
                        $classes = 'help-tab-content active';
                        foreach ($tabs as $tab):
                            $panel_id = "tab-panel-{$tab['id']}";
                            ?>

                            <div id="<?php echo esc_attr( $panel_id ); ?>" class="inside <?php echo $classes; ?>">
                                <?php
                                // Print tab content.
                                echo (string) $tab['content'];

                                // If it exists, fire tab callback.
                                if ( ! empty( $tab['callback'] )) {
                                    call_user_func_array( $tab['callback'], array( $this, $tab ) );
                                }
                                ?>
                            </div>
                            <?php
                            $classes  = 'help-tab-content';
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}