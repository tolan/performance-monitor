<?php

namespace PF\Main\Interfaces;

/**
 * Interface for observer objects.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Observer {

    /**
     * Receive update from subject.
     *
     * @param Subject $subject
     *
     * @return this
     */
    public function updateObserver (Observable $subject);
}
