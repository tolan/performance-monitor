<?php
/**
 * This file contains script for install application.
 * It means that it create database if not exists and create all tables with required data.
 */

include __DIR__.'/../boot.php';

\PF\scripts\Install\Manager::run(\PF\Main\Provider::getInstance());
