<?php
/**
 * Dump Die
 *
 * @param mixed ...$args
 */
function tr_dd(...$args) {
    \TypeRocket\Utility\Dump::die(...$args);
}

/**
 * Dump
 *
 * @param mixed ...$args
 */
function tr_dump(...$args) {
    \TypeRocket\Utility\Dump::data(...$args);
}

/**
 * Dots Walk
 *
 * Traverse array with dot notation.
 *
 * @param string $dots dot notation key.next.final
 * @param array|object $array an array to traverse
 * @param null|mixed $default
 *
 * @return array|mixed|null
 */
function tr_dots_walk($dots, $array, $default = null)
{
    return \TypeRocket\Utility\Data::walk(...func_get_args());
}

/**
 * Dots Set
 *
 * Set an array value using dot notation.
 *
 * @param string $dots dot notation path to set
 * @param array $array the original array
 * @param mixed $value the value to set
 *
 * @return array
 */
function tr_dots_set($dots, array $array, $value)
{
    return \TypeRocket\Utility\Arr::set(...func_get_args());
}

/**
 * HTML class names helper
 *
 * @param string $defaults
 * @param null|array $classes
 * @param string $failed
 * @return string
 */
function tr_class_names($defaults, $classes = null, $failed = '') {
    return \TypeRocket\Utility\Str::classNames(...func_get_args());
}

/**
 * Get Namespaced Class
 *
 * @param string $append
 * @return string
 */
function tr_app_class($append) {
    return \TypeRocket\Utility\Helper::appNamespace($append);
}

/**
 * Get WordPress Root
 *
 * @return string
 */
function tr_wp_root() {
    return \TypeRocket\Utility\Helper::wordPressRootPath();
}

/**
 * Resolve Class From DI Container
 *
 * Get from DI container
 *
 * @param string $class_name class or alias
 * @return mixed|null
 */
function tr_container($class_name) {
    return \TypeRocket\Core\Container::resolve($class_name);
}

/**
 * Resolve Class
 *
 * Inject all class dependencies
 *
 * @param string $class_name class or alias
 *
 * @return mixed|null
 * @throws Exception
 */
function tr_resolve($class_name) {
    return \TypeRocket\Core\Resolver::build($class_name);
}

/**
 * Updates _site_state_changed option in database
 *
 * Should be called when a theme or plugin has been activated or deactivated.
 * Used to facilitate tasks like flushing rewrite rules for the registration
 * and de-registration of post types and taxonomies.
 *
 * @link https://core.trac.wordpress.org/ticket/47526
 *
 * @param string|array $arg single function name or list of function names
 */
function tr_update_site_state($arg)
{
    \TypeRocket\Core\System::updateSiteState($arg);
}

/**
 * Locate Config Setting
 *
 * Traverse array with dot notation.
 *
 * @param string $dots dot notation key.next.final
 * @param null|mixed $default default value to return if null
 *
 * @return array|mixed|null
 */
function tr_config($dots, $default = null) {
    return \TypeRocket\Core\Config::get(...func_get_args());
}

/**
 * Get Main Response
 *
 * @return \TypeRocket\Http\Response
 */
function tr_response() {
    return \TypeRocket\Http\Response::getFromContainer();
}

/**
 * TypeRocket Nonce
 *
 * @param string $action
 *
 * @return false|string
 */
function tr_nonce($action = '') {
    return \TypeRocket\Http\Response::new()->createNonce(...func_get_args());
}

/**
 * Get Request
 *
 * @param null $method
 * @return \TypeRocket\Http\Request
 */
function tr_request() {
    return new \TypeRocket\Http\Request;
}

/**
 * Get Debug
 *
 * @return bool
 */
function tr_debug() {
    return \TypeRocket\Core\Config::get('app.debug');
}

/**
 * Get Assets URL
 *
 * @param string $append
 * @return string
 */
function tr_assets_url( $append ) {
    return \TypeRocket\Utility\Helper::assetsUrl(...func_get_args());
}

/**
 * Get Views Directory
 *
 * @param string $append
 * @return string
 */
function tr_storage_path( $append ) {
    return \TypeRocket\Utility\Path::storage($append);
}


/**
 * Get Views Directory
 *
 * @param string $append
 * @return string
 */
function tr_views_path( $append ) {
    return \TypeRocket\Utility\Path::views($append);
}

/**
 * Get controller by recourse
 *
 * @param string $resource use the resource name to get controller
 * @param bool $instance
 *
 * @return null|string $controller
 */
function tr_controller($resource, $instance = true)
{
    return \TypeRocket\Utility\Helper::controllerClass($resource, $instance);
}

/**
 * Get model by recourse
 *
 * @param string $resource use the resource name to get model
 * @param bool $instance
 *
 * @return null|string|\TypeRocket\Models\Model $object
 */
function tr_model($resource, $instance = true)
{
    return \TypeRocket\Utility\Helper::modelClass($resource, $instance);
}

/**
 * Register taxonomy
 *
 * @param string $singular
 * @param null $plural
 * @param array $settings
 *
 * @return \TypeRocket\Register\Taxonomy
 */
function tr_taxonomy($singular, $plural = null, $settings = [])
{
    return \TypeRocket\Register\Taxonomy::add(...func_get_args());
}

/**
 * Register post type
 *
 * @param string $singular Singular name for post type
 * @param string|null $plural Plural name for post type
 * @param array $settings The settings for the post type
 * @param string|null $id post type ID
 *
 * @return \TypeRocket\Register\PostType
 */
function tr_post_type($singular, $plural = null, $settings = [], $id = null )
{
    return \TypeRocket\Register\PostType::add(...func_get_args());
}

/**
 * Register meta box
 *
 * @param string $name
 * @param null|string|array $screen
 * @param array $settings
 *
 * @return \TypeRocket\Register\MetaBox
 */
function tr_meta_box($name = null, $screen = null, $settings = [])
{
    return \TypeRocket\Register\MetaBox::add(...func_get_args());
}

/**
 * @param string $resource
 * @param string $action
 * @param string $title
 * @param array $settings
 * @param null|array|string|callable $handler
 *
 * @return \TypeRocket\Register\Page
 */
function tr_page($resource, $action, $title, array $settings = [], $handler = null)
{
    return \TypeRocket\Register\Page::add(...func_get_args());
}

/**
 * @param string $singular
 * @param string|array|null $plural
 * @param array $settings
 * @param null $resource
 * @param null $handler
 *
 * @return \TypeRocket\Register\Page
 * @throws Exception
 */
function tr_resource_pages($singular, $plural = null, array $settings = [], $resource = null, $handler = null)
{
    return \TypeRocket\Register\Page::addResourcePages(...func_get_args());
}

/**
 * Create tabs
 *
 * @return \TypeRocket\Elements\Tabs
 */
function tr_tabs()
{
    return \TypeRocket\Elements\Tabs::new();
}

/**
 * Instance the From
 *
 * @param string|\TypeRocket\Interfaces\Formable|array|null $resource posts, users, comments, options your own
 * @param string|null $action update, delete, or create
 * @param null|int $item_id you can set this to null or an integer
 * @param mixed|null|string $model
 *
 * @return \TypeRocket\Elements\BaseForm|\App\Elements\Form
 */
function tr_form($resource = null, $action = null, $item_id = null, $model = null)
{
    return \TypeRocket\Utility\Helper::form(...func_get_args());
}

/**
 * Modify Model Value
 *
 * @param \TypeRocket\Models\Model $model use dot notation
 * @param mixed $args
 *
 * @return array|mixed|null|string
 */
function tr_model_field(\TypeRocket\Models\Model $model, $args)
{
    return \TypeRocket\Utility\ModelField::model(...func_get_args());
}

/**
 * Get the post's field
 *
 * @param string $name use dot notation
 * @param null|int|WP_Post $item_id
 *
 * @return array|mixed|null|string
 */
function tr_post_field($name, $item_id = null)
{
    return \TypeRocket\Utility\ModelField::post(...func_get_args());
}

/**
 * Get the post field
 *
 * @param string $name use dot notation
 * @param null|int|WP_Post $item_id
 *
 * @return array|mixed|null|string
 */
function tr_field($name, $item_id = null)
{
    return \TypeRocket\Utility\ModelField::post(...func_get_args());
}

/**
 * Get components
 *
 * Auto binding only for post types
 *
 * @param string $name use dot notation
 * @param null $item_id
 *
 * @param null|string $modelClass
 *
 * @return array|mixed|null|string
 * @throws Exception
 */
function tr_components_field($name, $item_id = null, $modelClass = null)
{
    return \TypeRocket\Utility\ModelField::components(...func_get_args());
}

/**
 * Loop Components
 *
 * @param array $builder_data
 * @param array $other be sure to pass $name, $item_id, $model
 * @param string $group
 */
function tr_components_loop($builder_data, $other = [], $group = 'builder')
{
    \TypeRocket\Elements\Fields\Matrix::componentsLoop(...func_get_args());
}

/**
 * Is TypeRocket Builder Active
 *
 * @param string|null $field_name
 *
 * @return bool
 */
function tr_show_page_builder(string $field_name = "use_builder", $item_id = null)
{
    $args = func_get_args() ?: [$field_name];
    $use_builder = tr_post_field(...$args);
    return $use_builder === '1' || $use_builder === 1 && !post_password_required();
}

/**
 * Get users field
 *
 * @param string $name use dot notation
 * @param null $item_id
 *
 * @return array|mixed|null|string
 */
function tr_user_field($name, $item_id = null)
{
    return \TypeRocket\Utility\ModelField::user(...func_get_args());
}

/**
 * Get options
 *
 * @param string $name use dot notation
 *
 * @return array|mixed|null|string
 */
function tr_option_field($name)
{
    return \TypeRocket\Utility\ModelField::option(...func_get_args());
}

/**
 * Get comments field
 *
 * @param string $name use dot notation
 * @param null|int $item_id
 *
 * @return array|mixed|null|string
 */
function tr_comment_field($name, $item_id = null)
{
    return \TypeRocket\Utility\ModelField::comment(...func_get_args());
}

/**
 *  Get taxonomy field
 *
 * @param string|array $name use dot notation
 * @param string|null $taxonomy taxonomy model class
 * @param int|null $item_id taxonomy id
 *
 * @return array|mixed|null|string
 */
function tr_term_field($name, $taxonomy = null, $item_id = null)
{
    return \TypeRocket\Utility\ModelField::term(...func_get_args());
}

/**
 * Get resource
 *
 * @param string $name use dot notation
 * @param string $resource
 * @param null|int $item_id
 *
 * @return array|mixed|null|string
 */
function tr_resource_field($name, $resource, $item_id = null)
{
    return \TypeRocket\Utility\ModelField::resource(...func_get_args());
}

/**
 * Detect is JSON
 *
 * @param $args
 *
 * @return bool
 */
function tr_is_json(...$args)
{
    return \TypeRocket\Utility\Data::isJson(...$args);
}

/**
 * @return \TypeRocket\Http\Redirect
 */
function tr_redirect()
{
    return \TypeRocket\Http\Redirect::new();
}

/**
 * @param null|array $default
 * @param bool $delete
 *
 * @return array
 */
function tr_redirect_message($default = null, $delete = true)
{
    return \TypeRocket\Http\Cookie::new()->redirectMessage(...func_get_args());
}

/**
 * @param null $default
 *
 * @return array
 */
function tr_redirect_errors($default = null)
{
    return \TypeRocket\Http\Cookie::new()->redirectErrors(...func_get_args());
}

/**
 * @param null $default
 * @param bool $delete
 *
 * @return array
 */
function tr_redirect_data($default = null, $delete = true)
{
    return \TypeRocket\Http\Cookie::new()->redirectData(...func_get_args());
}

/**
 * TypeRocket Nonce Field
 *
 * @param string $action
 *
 * @return string
 */
function tr_field_nonce($action = '')
{
    return \TypeRocket\Elements\BaseForm::nonceInput(...func_get_args());
}

/**
 * TypeRocket Nonce Field
 *
 * @param string $method GET, POST, PUT, PATCH, DELETE
 *
 * @return string
 */
function tr_field_method($method = 'POST')
{
    return \TypeRocket\Elements\BaseForm::methodInput(...func_get_args());
}

/**
 * TypeRocket Nonce Field
 *
 * @param string $method GET, POST, PUT, PATCH, DELETE
 * @param string $prefix prefix fields will be grouped under
 *
 * @return string
 */
function tr_form_hidden_fields($method = 'POST', $prefix = 'tr')
{
    return \TypeRocket\Elements\BaseForm::hiddenInputs(...func_get_args());
}

/**
 * Check Spam Honeypot
 *
 * @param null|string $name
 *
 * @return \TypeRocket\Html\Html
 */
function tr_honeypot_fields($name = null)
{
    return \TypeRocket\Elements\BaseForm::honeypotInputs(...func_get_args());
}

/**
 * @return \TypeRocket\Http\Cookie
 */
function tr_cookie()
{
    return new \TypeRocket\Http\Cookie();
}

/**
 * @param string $dots
 * @param array $data
 * @param string $ext
 *
 * @return \TypeRocket\Template\View
 */
function tr_view($dots, array $data = [], $ext = '.php')
{
    return \TypeRocket\Template\View::new(...func_get_args());
}

/**
 * Validate fields
 *
 * @param array $rules
 * @param array|null $fields
 * @param null $modelClass
 * @param bool $run
 *
 * @return \TypeRocket\Utility\Validator
 */
function tr_validator($rules, $fields = null, $modelClass = null, $run = false)
{
    return \TypeRocket\Utility\Validator::new(...func_get_args());
}

/**
 * Route
 *
 * @return \TypeRocket\Http\Route
 */
function tr_route()
{
    return \TypeRocket\Http\Route::new();
}

/**
 * Get Routes Repo
 *
 * @return \TypeRocket\Http\RouteCollection
 */
function tr_routes_repo()
{
    return \TypeRocket\Http\RouteCollection::getFromContainer();
}

/**
 * Get Routes Repo
 * @param string $name
 * @return null|\TypeRocket\Http\Route
 */
function tr_route_find($name)
{
    return \TypeRocket\Http\RouteCollection::getFromContainer()->getNamedRoute($name);
}

/**
 * Get Routes Repo
 * @param string $name
 * @param array $values
 * @param bool $site
 *
 * @return null|string
 */
function tr_route_url($name, $values = [], $site = true)
{
    return \TypeRocket\Http\Route::buildUrl(...func_get_args());
}

/**
 * Database Query
 *
 * @return \TypeRocket\Database\Query
 */
function tr_query()
{
    return \TypeRocket\Database\Query::new();
}

/**
 * File Utility
 *
 * @param string $file
 * @return object
 * @throws Exception
 */
function tr_file($file) {
    return \TypeRocket\Utility\File::new($file);
}

/**
 * Get Asset Version
 *
 * @param string $path
 * @param string $namespace
 * @return mixed
 */
function tr_manifest_cache($path, $namespace) {
    return \TypeRocket\Utility\Manifest::cache(...func_get_args());
}

/**
 * Get Asset Version
 *
 * @param string $namespace
 * @return \TypeRocket\Utility\RuntimeCache
 */
function tr_manifest($namespace = 'typerocket') {
    return \TypeRocket\Utility\Manifest::getFromRuntimeCache(...func_get_args());
}

/**
 * Throw HTTP Error
 *
 * @param int $code
 *
 * @return mixed
 */
function tr_abort(int $code) {
    \TypeRocket\Exceptions\HttpError::abort($code);
}

/**
 * Enable Front-end
 */
function tr_frontend_enable()
{
    \TypeRocket\Core\System::getFromContainer()->frontendEnable();
}

/**
 * Sanitize Editor
 *
 * @param $content
 * @param bool $force_filter
 * @param bool $auto_p
 *
 * @return string
 */
function tr_sanitize_editor($content, $force_filter = true, $auto_p = false)
{
    return \TypeRocket\Utility\Sanitize::editor($content, $force_filter, $auto_p);
}

/**
 * @param array|null $data keys include message and type
 * @param bool $dismissible
 *
 * @return false|string|null
 */
function tr_flash_message($data = null, $dismissible = false)
{
    return \TypeRocket\Elements\Notice::flash(...func_get_args());
}

/**
 * @param array|object|\ArrayObject $value
 *
 * @return \TypeRocket\Utility\Nil
 */
function tr_nils($value)
{
    return \TypeRocket\Utility\Data::nil($value);
}

/**
 * @param string $folder
 *
 * @return \TypeRocket\Utility\PersistentCache
 */
function tr_cache($folder = 'app')
{
    return \TypeRocket\Utility\PersistentCache::new($folder);
}

/**
 * Roles
 *
 * @return \TypeRocket\Auth\Roles
 */
function tr_roles()
{
    return \TypeRocket\Auth\Roles::new();
}
