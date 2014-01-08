<?php

namespace PF\Main\Web\Controller\Abstracts;

use PF\Main\Provider;

/**
 * Abstract class for all controllers.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Controller {

    /**
     * Provider instance
     *
     * @var \PF\Main\Provider
     */
    private $_provider = null;

    /**
     * Action name
     *
     * @var string
     */
    private $_action = null;

    /**
     * Parameters for action
     *
     * @var array
     */
    private $_params = null;

    /**
     * Data from action
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instnace
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
        $this->init();
    }

    /**
     * Optional init method (instead of construct)
     *
     * @return void
     */
    protected function init() {}

    /**
     * Sets action name (without 'action' at begin).
     *
     * @param string $action Action name
     *
     * @return \PF\Main\Web\Controller\Abstracts\Controller
     */
    final public function setAction($action = null) {
        $this->_action = $action;

        return $this;
    }

    /**
     * Gets action name
     *
     * @return string Action name
     */
    final public function getAction() {
        return $this->_action;
    }

    /**
     * Sets parameters for action.
     *
     * @param array $params Array with parameters
     *
     * @return \PF\Main\Web\Controller\Abstracts\Controller
     */
    final public function setParams($params = null) {
        $this->_params = $params;

        return $this;
    }

    /**
     * Run action on controller. It call action, set data to view and set view into response.
     *
     * @return \PF\Main\Web\Controller\Abstracts\Controller
     */
    final public function run() {
        if ($this->_params === null) {
            $answer = $this->{'action'.ucfirst($this->_action)}();
        } else {
            $answer = $this->{'action'.ucfirst($this->_action)}($this->_params);
        }

        // If data was not set with $this->setData() then set data from returned action
        if ($answer && $this->getData() === null) {
            $this->setData($answer);
        }

        $view = $this->getView();
        $view->setData($this->getData());
        $this->getResponse()->setView($view);

        return $this;
    }

    /**
     * Sets data for view (it should be used in action).
     *
     * @param mixed $data Data for view
     *
     * @return \PF\Main\Web\Controller\Abstracts\Controller
     */
    final protected function setData($data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * Returns data for view.
     *
     * @return mixed
     */
    final protected function getData() {
        return $this->_data;
    }

    /**
     * Gets provider instance.
     *
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Gets request instance.
     *
     * @return \PF\Main\Web\Component\Request
     */
    final protected function getRequest() {
        return $this->_provider->get('request');
    }

    /**
     * Abstract method for returns response instance.
     *
     * @return \PF\Main\Web\Component\Response
     */
    abstract public function getResponse();

    /**
     * Abstract method for returns view instance.
     *
     * @return \PF\Main\Web\View\AbstractView
     */
    abstract protected function getView();
}
