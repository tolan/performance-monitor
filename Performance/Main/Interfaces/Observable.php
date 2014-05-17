<?php

namespace PF\Main\Interfaces;

/**
 * Interface for observable objects.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Observable {

    /**
     * Attach an Observer.
     *
     * @param Observer $observer
     *
     * @return this
     */
    public function attach (Observer $observer);

    /**
     * Detach an observer.
     *
     * @param Observer $observer
     *
     * @return this
     */
    public function detach (Observer $observer);

    /**
     * Notify an observer.
     *
     * @return this
     */
    public function notify ();
}
