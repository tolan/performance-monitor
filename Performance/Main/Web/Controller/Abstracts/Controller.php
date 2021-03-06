<?php

namespace PM\Main\Web\Controller\Abstracts;

use PM\Main\Provider;
use PM\Main\Commander;

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
     * @var \PM\Main\Provider
     */
    private $_provider = null;

    /**
     * Commander instance.
     *
     * @var \PM\Main\Commander
     */
    private $_commander = null;

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
     * @param \PM\Main\Provider  $provider  Provider instnace
     * @param \PM\Main\Commander $commander Commander instnace
     *
     * @return void
     */
    final public function __construct(Provider $provider, Commander $commander) {
        $this->_provider  = $provider;
        $this->_commander = $commander;
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
     * @return \PM\Main\Web\Controller\Abstracts\Controller
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
     * @return \PM\Main\Web\Controller\Abstracts\Controller
     */
    final public function setParams($params = null) {
        $this->_params = $params;

        return $this;
    }

    /**
     * Run action on controller. It call action, set data to view and set view into response.
     *
     * @return \PM\Main\Web\Controller\Abstracts\Controller
     */
    final public function run() {
        $this->getExecutor()->getResult()
            ->fromArray($this->_params)
            ->set('input', $this->getRequest()->getInput());

        $method = 'action'.ucfirst($this->_action);
        $answer = forward_static_call_array(array($this, $method), (array)$this->_params);

        // If data was not set with $this->setData() then set data from returned action
        if ($answer !== null && $this->getData() === null) {
            $this->setData($answer);
        }

        // If data was not set then use executor
        if ($answer === null && $this->getData() === null) {
            $result = $this->getExecutor()->execute();
            $data   = $result->hasData() ? $result->getData() : null;
            $this->setData($data);
        }

        $view = $this->getView();
        $view->setData($this->getData());
        $this->getResponse()->setView($view);

        return $this;
    }

    /**
     * Returns data for view.
     *
     * @return mixed
     */
    final public function getData() {
        return $this->_data;
    }

    /**
     * Sets data for view (it should be used in action).
     *
     * @param mixed $data Data for view
     *
     * @return \PM\Main\Web\Controller\Abstracts\Controller
     */
    final protected function setData($data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * Gets provider instance.
     *
     * @return \PM\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Gets request instance.
     *
     * @return \PM\Main\Web\Component\Request
     */
    final protected function getRequest() {
        return $this->_provider->get('request');
    }

    /**
     * Returns executor instance for sharing result and using common scope.
     *
     * @return \PM\Main\Commander\Executor
     */
    final protected function getExecutor() {
        return $this->_commander->getExecutor($this->getAction());
    }

    /**
     * Abstract method for returns response instance.
     *
     * @return \PM\Main\Web\Component\Response
     */
    abstract public function getResponse();

    /**
     * Abstract method for returns view instance.
     *
     * @return \PM\Main\Web\View\AbstractView
     */
    abstract protected function getView();
}
