<?php
namespace Aoloe;

// use function Aoloe\debug as debug;

class Module {
    private $module = null;
    private $parameter = null;
    public function set($module, $parameter) {
        $this->module = $current;
        $this->parameter = $parameter;
    }
    public function set_module($module) {$this->module = $module;}
    public function set_parameter($parameter) {$this->parameter = $parameter;}

    private $page = null;
    public function set_page($page) {$this->page = $page;}
    private $page_query = null;

    private $site = null;
    public function set_site($site) {$this->site = $site;}

    private $module_default = 'Page';
    public function set_module_default($module) {$this->module_default = $module_404;}
    private $module_404 = 'Error_404';
    public function set_module_404($module) {$this->module_404 = $module_404;}
    private $url_redirect = null;
    private $module_301 = 'Error_301';
    public function set_module_301($module) {$this->module_301 = $module_301;}
    private $redirect_301 = null; // array of urls to be redirected if not found and target
    public function set_redirect_301($redirect) {$this->redirect_301 = $redirect_301;}



    /**
     * @return if null no further rendering necessary, otherwise integrage
     * the returned value in the main template's content
     */
    public function get_rendered() {
        $result = null;
        list ($module_name, $parameter) = $this->get_current();
        // debug('page', $this->page);
        $result = "<p>Module ".$module_name." is not valid.</p>\n";
        $module_file = 'module/'.$module_name.'.php';
        if (file_exists($module_file)) {
            include_once($module_file);
            if (class_exists($module_name)) {
                $module = new $module_name();
                $module->set_site($this->site);
                // debug('parameter', $parameter);
                if (isset($parameter)) {
                    foreach ($parameter as $key => $value) {
                        if (method_exists(get_class($module), 'set_'.$key)) {
                            // debug('key', $key);
                            // debug('value', $value);
                            $module->{'set_'.$key}($value);
                        }
                    }
                }
                $result = $module->get_content();
            }
        }
        return $result;
    }

    private function get_current() {
        $module_name = null;
        $parameter = null;
        //debug('page', $this->page);
        $parameter = isset($this->parameter) ? $this->parameter : array();
        if (isset($this->module)) {
            $module_name = $this->module;
        } elseif (isset($this->page)) {
            if (array_key_exists('module', $this->page)) {
                if (is_array($this->page['module'])) {
                    $module_name = $this->page['module']['name'];
                    if (array_key_exists('parameter', $this->page['module'])) {
                        $parameter = array_merge($parameter, $this->page['module']['parameter']);
                    }
                } else {
                    $module_name = $this->page['module'];
                }
            } else {
                $module_name = $this->module_default;
            }
        } else {
            list($module_name, $parameter) = $this->get_error();
        }
        return array($module_name, $parameter);
    }

    function get_error() {
        $module_name = null;
        $parameter = null;
        if (isset($this->url_redirect) && isset($this->parameter)) {
            if (array_key_exists($this->parameter, $this->url_redirect)) {
                $module_name = $this->module_301;
                $parameter = array('redirect' => $this->url_redirect[$url_request]);
            }
        }
        if (is_null($module_name)) {
            $module_name = $this->module_404;
        }
        return array($module_name, $parameter);
    }
}

class Module_abstract {
    protected $site = null;
    public function set_site($site) {$this->site = $site;}
}

