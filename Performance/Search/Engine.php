<?php

namespace PF\Search;

use PF\Main\Provider;
use PF\Main\Utils;

/**
 * This script defines class for search engine. It provides method for find entities by given filters.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Engine {

    /**
     * Provider instance
     *
     * @var \PF\Main\Provider
     */
    private $_provider;

    /**
     * Utils instance
     *
     * @var \PF\Main\Utils
     */
    private $_utils;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     */
    public function __construct(Provider $provider, Utils $utils) {
        $this->_provider = $provider;
        $this->_utils    = $utils;
    }

    /**
     * This method provides finding entities by given search template.
     *
     * @param array $template Template of filters
     *
     * @return array
     *
     * @throws Exception Throws when target of filters is not same as default target.
     */
    public function find($template) {
        $association = $this->_provider->get('PF\Search\Association'); /* @var $association \PF\Search\Association */
        $logicAnalyzator = $this->_provider->prototype('PF\Main\Logic\Analyzator'); /* @var $logicAnalyzator \PF\Main\Logic\Analyzator */
        $logicAnalyzator->setExpression($template['logic']);
        $logicEvaluator = $this->_provider->prototype('PF\Main\Logic\Evaluator'); /* @var $logicEvaluator \PF\Main\Logic\Evaluator */
        $logicEvaluator->setLogic($logicAnalyzator->getLogic());

        $groups = $template['groups'];
        $target = $template['target'];
        $result = array();

        if ($association->isAllowedLogic($target)) {
            $result['all'] = $this->_searchAll($target);
            $logicEvaluator->setScope($result['all']);
        }

        foreach ($groups as $key => $group) {
            $result[$key] = $this->_searchByGroup($group['filters'], $target);
            $logicEvaluator->setData($key, $result[$key]);
        }

        $result['result'] = $logicEvaluator->getResult();

        return $result;
    }

    /**
     * This method provides finding all entity.
     * ATTENTION it can returns big amount data.
     *
     * @param enum $target One of enum \PF\Search\Enum\Target
     *
     * @return array
     */
    private function _searchAll($target) {
        $targetInstance = $this->_provider->prototype('PF\Search\Filter\Target\\' . ucfirst($target)); /* @var $targetInstance Filter\Target\AbstractTarget */

        $container  = $this->_provider->prototype('PF\Search\Filter\Container'); /* @var $container Filter\Container */
        $container->setTarget($targetInstance);

        return $container->fetchAll();
    }

    /**
     * This method provides finding entities by filters of group.
     *
     * @param array $filters List of search filters
     * @param enum  $target  \PF\Search\Enum\Target
     *
     * @return array
     *
     * @throws Exception Throws when target and filter target is not same.
     */
    private function _searchByGroup($filters, $target) {
        $targetInstance = $this->_provider->prototype('PF\Search\Filter\Target\\' . ucfirst($target)); /* @var $targetInstance Filter\Target\AbstractTarget */

        $container = $this->_provider->prototype('PF\Search\Filter\Container'); /* @var $container Filter\Container */
        $container->setTarget($targetInstance);

        foreach ($filters as $filter) {
            if (isset($filter['target']) && $filter['target'] !== $target) {
                throw new Exception('Target of filter is different to default target.');
            }

            $junction = $this->_provider
                ->prototype('PF\Search\Filter\Junction\\' . ucfirst($filter['target'])); /* @var $junction Filter\Junction\AbstractJunction */

            $condition = $this->_provider
                ->prototype('PF\Search\Filter\Condition\\' . ucfirst($filter['type'])); /* @var $condition Filter\Condition\AbstractCondition */

            $container->addFilter($filter, $junction, $condition);
        }

        return $container->fetchAll();
    }
}
