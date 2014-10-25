<?php

namespace PM\Statistic\Engine\Source;

use PM\Main\Provider;
use PM\Statistic\Entity\Template;

/**
 * This script defines abstract class for creation source select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
abstract class AbstractSource {

    /**
     * Provider instance.
     *
     * @var Provider
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
     * Abstract method for creation source select by statistic template entity.
     *
     * @param Template $template Statistic template entity instance
     *
     * @return \PM\Main\Database\Select
     */
    abstract public function getSelect(Template $template);

    /**
     * Returns provider instance.
     *
     * @return Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }
}
