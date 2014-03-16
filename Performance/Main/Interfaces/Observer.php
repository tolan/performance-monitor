<?php

namespace PF\Main\Interfaces;

interface Observer {

    /**
     * Receive update from subject.
     *
     * @param Subject $subject
     *
     * @return this
     */
    public function update (Observable $subject);
}
