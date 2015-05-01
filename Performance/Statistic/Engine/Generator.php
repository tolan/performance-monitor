<?php

namespace PM\Statistic\Engine;

use PM\Main\Provider;
use PM\Main\Database\Select;
use PM\Statistic\Entity;
use PM\Statistic\Enum\View\Base;
use PM\Search;

/**
 * This script defines class for generate statistics about entities.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Generator {

    /**
     * Provider instance.
     *
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * It generates statistics and returns statistic data by template entity.
     *
     * @param Entity\Template $template Statistic template entity
     *
     * @return array
     */
    public function generateStatistic(Entity\Template $template) {
        $searchTemplate = new Search\Entity\Template($template->getSource()['template']);
        $sourceSelect   = $this->_getSourceSelect($template);

        $result = array();
        foreach ($template->getViews() as $view) {
            $select   = $this->_getSelectForView($view, clone $sourceSelect, $searchTemplate);
            $viewData = $select->fetchAll();
            $result   = array_merge($result, $this->_transformData($view, $viewData));
        }

        return $result;
    }

    /**
     * Returns select for source items.
     *
     * @param Entity\Template $template Statistic template instance
     *
     * @return \PM\Main\Database\Select
     */
    private function _getSourceSelect(Entity\Template $template) {
        $source = $this->_provider->prototype('PM\Statistic\Engine\Source\\'.ucfirst($template->getSource()['type']));
        /* @var $source \PM\Statistic\Engine\Source\AbstractSource */

        return $source->getSelect($template);
    }

    /**
     * Returns statistic select for template view.
     *
     * @param Entity\View            $view         Statistic template view entity instance
     * @param Select                 $sourceSelect Source select instance
     * @param Search\Entity\Template $template     Search template entity instance
     *
     * @return Select
     */
    private function _getSelectForView(Entity\View $view, Select $sourceSelect, Search\Entity\Template $template) {
        $container = $this->_provider->prototype('PM\Statistic\Engine\Container'); /* @var $container \PM\Statistic\Engine\Container */
        $container
            ->setSource($template->getTarget(), $sourceSelect)
            ->setTarget($view->getTarget(), $this->_provider->singleton('PM\Statistic\Engine\Target\\'.ucfirst($view->getTarget())));

        $dataBase = $view->getType();
        $wrapper  = $this->_provider->prototype('PM\Statistic\Engine\Wrapper\\'.ucfirst($dataBase));
        $viewData = $this->_provider->prototype('PM\Statistic\Engine\Data\\'.ucfirst($view->getTarget()));
        /* @var $wrapper Wrapper\AbstractWrapper */
        /* @var $viewData Data\AbstractData */

        foreach ($view->get('lines', array()) as $line) {
            $lineMethod = $line->getType();
            $alias      = $this->_getLineAlias($line);

            $function = $this->_provider->prototype('PM\Statistic\Engine\Functions\\'. ucfirst($line->getFunction()));
            /* @var $function Functions\AbstractFunction */
            $function->setTable($view->getTarget())
                ->setAlias($alias)
                ->setValue($line->getValue());

            $container->addLine($viewData, $function, $lineMethod);
            $wrapper->addColumn($alias);
        }

        $select = $container->getSelect();

        if ($dataBase === Base::TIME) {
            $viewData->addTime($select);
        }

        $wrapper->setSourceSelect($select);

        return $wrapper->getSelect();
    }

    /**
     * Returns alias for statistic select by line entity. It should be unique in statistic select.
     *
     * @param Entity\Line $line Statistic line entity instance
     *
     * @return string
     */
    private function _getLineAlias(Entity\Line $line) {
        return $line->getType().'_'.$line->getFunction().'_'.$line->getId();
    }

    /**
     * It transforms statistic data to statistic data entity.
     *
     * @param Entity\View $view     Statistic view entity instance
     * @param array       $viewData Statistic data from select
     *
     * @return Entity\Statistic[]
     */
    private function _transformData(Entity\View $view, $viewData) {
        $aliases  = array();
        foreach ($view->get('lines', array()) as $line) { /* @var $line Entity\Line */
            $aliases[$this->_getLineAlias($line)] = $line->getId();
        }

        $result = array();
        foreach ($viewData as $data) {
            foreach ($data as $alias => $line) {
                if ($alias !== 'time') {
                    $time = null;

                    if (array_key_exists('time', $data) === true) {
                        $time = is_int($data['time']) ? $data['time'] : strtotime($data['time']);
                    }

                    $result[] = new Entity\Statistic(
                        array(
                            'lineId' => $aliases[$alias],
                            'time'   => $time,
                            'value'  => $line
                        )
                    );
                }
            }
        }

        return $result;
    }
}
