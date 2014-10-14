<?php

namespace PF\Statistic\Engine\Source;

use PF\Statistic\Entity;
use PF\Search;

/**
 * This script defines class for creation source select with actual items from search engine.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Template extends AbstractSource {

    /**
     * Returns source select with set of actual item from search engine.
     *
     * @param Entity\Template $template Statistic template entity
     *
     * @return \PF\Search\Filter\Select
     */
    public function getSelect(Entity\Template $template) {
        $searchTemplate = new Search\Entity\Template($template->getSource()['template']);

        $searchEngine   = $this->getProvider()->get('PF\Search\Engine'); /* @var $searchEngine \PF\Search\Engine */
        $searchSelects  = $searchEngine->createSearchSelect($searchTemplate);

        return $searchSelects['result'];
    }
}
