<?php

namespace PF\Search;

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
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param \Performance_Main_Provider $provider Provider instance
     */
    public function __construct(\Performance_Main_Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * This method provides finding entities by given target entity and set of filters.
     *
     * @param array $filters Set of filters
     * @param enum  $target  One of PF\Search\Enum\Target
     *
     * @return array
     *
     * @throws Exception Throws when target of filters is not same as default target.
     */
    public function find($filters, $target) {
        $connection = $this->_provider->get('database')->getConnection();
        $this->_provider->set($connection);

        $targetInstance = $this->_provider->prototype('PF\Search\Filter\Target\\' . ucfirst($target)); /* @var $targetInstance Filter\Target\AbstractTarget */

        $container = $this->_provider->prototype('PF\Search\Filter\Container'); /* @var $container Filter\Container */
        $container->setTarget($targetInstance);

        foreach ($filters as $filter) {
            if ($filter['target'] !== $target) {
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
