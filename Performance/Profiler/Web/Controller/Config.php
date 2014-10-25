<?php

namespace PM\Profiler\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Http;
use PM\Profiler\Monitor;

/**
 * This scripts defines class of profiler config controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 *
 * @link /profiler/config
 */
class Config extends Json {

    /**
     * Gets information about request and their parameters methods.
     *
     * @link /request/methods
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetMethods() {
        $methods = Http\Enum\Method::getSelection('profiler.scenario.request.method.');

        $result = array(
            'requests' => $methods
        );

        $paramMethods = Http\Enum\ParameterType::getAllowedParams();
        foreach ($paramMethods as $method => $allowed) {
            foreach ($allowed as $allow) {
                $result['params'][$method][] = array(
                    'value' => $allow,
                    'name'  => 'profiler.scenario.request.method.'.$allow
                );
            }
        }

        $this->setData($result);
    }

    /**
     * Gets options for filters
     *
     * @link /filter/options
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetFilterOptions() {
        $this->getExecutor()->add(function($data = array()) {
            $data['types'] = Monitor\Filter\Enum\Type::getSelection('profiler.scenario.request.filter.type.');

            return array('data' => $data);
        })->add(function($data = array()) {
            $data['params'] = Monitor\Filter\Association::getAssociation();

            return array('data' => $data);
        });
    }
}
