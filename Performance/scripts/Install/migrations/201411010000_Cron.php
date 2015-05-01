<?php

namespace PM\scripts\Install\migrations;

use PM\scripts\Install\AbstractMigration;

/**
 * This script defines migration class for install cron module.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
class Cron_201411010000 extends AbstractMigration {

    /**
     * It runs migration.
     *
     * @return void
     */
    public function run() {
        $this->loadSQLFile(__DIR__.'/data/201411010000_Cron.sql');
    }
}
