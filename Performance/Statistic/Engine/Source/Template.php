<?php

namespace PM\Statistic\Engine\Source;

use PM\Statistic\Entity;
use PM\Search;

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
     * @return \PM\Search\Filter\Select
     */
    public function getSelect(Entity\Template $template) {
        $searchTemplate = new Search\Entity\Template($template->getSource()['template']);

        $searchEngine   = $this->getProvider()->get('PM\Search\Engine'); /* @var $searchEngine \PM\Search\Engine */
        $searchSelects  = $searchEngine->createSearchSelect($searchTemplate);

        return $searchSelects['result'];
    }
}
