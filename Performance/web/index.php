<?php

// @codingStandardsIgnoreStart
include $_SERVER['DOCUMENT_ROOT'].'/Performance/boot.php';
// @codingStandardsIgnoreEnd
try {
    Performance_Main_Provider::getInstance()->get('web')->run();
} catch (Performance_Main_Exception $e) {
    Performance_Main_Provider::getInstance()->get('log')->error($e->getMessage());
    Performance_Main_Provider::getInstance()->get('log')->error($e->getTraceAsString());
}

