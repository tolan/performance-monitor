<?php

namespace PM\Search;

use PM\Main\Provider;
use PM\Main\Utils;
use PM\Main\Database;

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
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * Utils instance
     *
     * @var \PM\Main\Utils
     */
    private $_utils;

    /**
     * Construct method.
     *
     * @param \PM\Main\Provider $provider Provider instance
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
        $association = $this->_provider->get('PM\Search\Association'); /* @var $association \PM\Search\Association */
        $logicAnalyzator = $this->_provider->prototype('PM\Main\Logic\Analyzator'); /* @var $logicAnalyzator \PM\Main\Logic\Analyzator */
        $logicAnalyzator->setExpression($template['logic']);
        $logicEvaluator = $this->_provider->prototype('PM\Main\Logic\Evaluator'); /* @var $logicEvaluator \PM\Main\Logic\Evaluator */
        $logicEvaluator->setLogic($logicAnalyzator->getLogic());

        $groups = $template['groups'];
        $target = $template['target'];
        $result = array();

        if ($association->isAllowedLogic($target)) {
            $result['all'] = $this->_createSearcherForScope($target);
            $logicEvaluator->setScope($result['all']);
        }

        foreach ($groups as $key => $group) {
            $result[$key] = $this->_createSearcherByGroup($group['filters'], $target)->fetchAll();
            $logicEvaluator->setData($key, $result[$key]);
        }

        $result['result'] = $logicEvaluator->getResult();

        return $result;
    }

    /**
     * This method creates searching selects for entities by given search template.
     *
     * @param \PM\Search\Entity\Template $template Template of filters
     *
     * @return \PM\Search\Filter\Select[]
     */
    public function createSearchSelect(Entity\Template $template) {
        $association = $this->_provider->get('PM\Search\Association'); /* @var $association \PM\Search\Association */
        $logicAnalyzator = $this->_provider->prototype('PM\Main\Logic\Analyzator'); /* @var $logicAnalyzator \PM\Main\Logic\Analyzator */
        $logicAnalyzator->setExpression($template->getLogic());
        $performer = $this->_provider->prototype('PM\Main\Logic\Evaluate\Databases\Performer');
        /* @var $performer \PM\Main\Logic\Evaluate\Databases\Performer */
        $logicEvaluator = $this->_provider->prototype('PM\Main\Logic\Evaluator'); /* @var $logicEvaluator \PM\Main\Logic\Evaluator */
        $logicEvaluator->setLogic($logicAnalyzator->getLogic())->setPerformer($performer);

        $groups = $template->getGroups();
        $target = $template->getTarget();
        $result = array();

        if ($association->isAllowedLogic($target)) {
            $result['all'] = $this->_createSearcherForScope($target)->getSelect();
            $result['all']->resetPart(Database\Select::PART_COLUMNS)->columns('target.id');
            $logicEvaluator->setScope($result['all']);
        }

        foreach ($groups as $key => $group) {
            if (array_key_exists('identificator', $group) === true) {
                $key = $group['identificator'];
            }

            $result[$key] = $this->_createSearcherByGroup($group['filters'], $target)->getSelect();
            $result[$key]->resetPart(Database\Select::PART_COLUMNS)->columns('target.id');
            $logicEvaluator->setData($key, $result[$key]);
        }

        $result['result'] = $logicEvaluator->getResult();

        return $result;
    }

    /**
     * This method provides finding all entity.
     * ATTENTION it can returns big amount data.
     *
     * @param enum $target One of enum \PM\Search\Enum\Target
     *
     * @return Filter\Container
     */
    private function _createSearcherForScope($target) {
        $targetInstance = $this->_provider->prototype('PM\Search\Filter\Target\\' . ucfirst($target)); /* @var $targetInstance Filter\Target\AbstractTarget */

        $container  = $this->_provider->prototype('PM\Search\Filter\Container'); /* @var $container Filter\Container */
        $container->setTarget($targetInstance);

        return $container;
    }

    /**
     * This method provides finding entities by filters of group.
     *
     * @param array $filters List of search filters
     * @param enum  $target  \PM\Search\Enum\Target
     *
     * @return array
     *
     * @throws Exception Throws when target and filter target is not same.
     */
    private function _createSearcherByGroup($filters, $target) {
        $targetInstance = $this->_provider->prototype('PM\Search\Filter\Target\\' . ucfirst($target)); /* @var $targetInstance Filter\Target\AbstractTarget */

        $container = $this->_provider->prototype('PM\Search\Filter\Container'); /* @var $container Filter\Container */
        $container->setTarget($targetInstance);

        $associaton = $this->_provider->prototype('PM\Search\Association'); /* @var $associaton \PM\Search\Association */


        foreach ($filters as $filter) {
            if (isset($filter['target']) && $filter['target'] !== $target) {
                throw new Exception('Target of filter is different to default target.');
            }

            $junction = $this->_provider
                ->prototype('PM\Search\Filter\Junction\\' . ucfirst($filter['target'])); /* @var $junction Filter\Junction\AbstractJunction */

            if (array_key_exists('type', $filter) === false) {
                $filter['type'] = $associaton->getFilter($filter['target'], $filter['filter'])['type'];
            }

            $condition = $this->_provider
                ->prototype('PM\Search\Filter\Condition\\' . ucfirst($filter['type'])); /* @var $condition Filter\Condition\AbstractCondition */

            $container->addFilter($filter, $junction, $condition);
        }

        return $container;
    }
}
