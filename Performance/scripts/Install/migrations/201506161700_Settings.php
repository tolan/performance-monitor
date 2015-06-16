<?php

namespace PM\scripts\Install\migrations;

use PM\scripts\Install\AbstractMigration;

/**
 * This script defines migration class for install gearman module.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
class Settings_201506161700 extends AbstractMigration {

    /**
     * It runs migration.
     *
     * @return void
     */
    public function run() {
        $this->loadSQLFile(__DIR__.'/data/201506161700_Settings.sql');
    }
}
