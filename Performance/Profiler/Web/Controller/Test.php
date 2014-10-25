<?php

namespace PM\Profiler\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Database;

/**
 * This scripts defines class of profiler test controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 *
 * @link /profiler/test
 */
class Test extends Json {

    /**
     * Get test by given id.
     *
     * @link /mysql/get/{testId}
     *
     * @method GET
     *
     * @param int $testId Test ID of MySQL scenario
     *
     * @return void
     */
    public function actionGetTest($testId) {
        $testService = $this->getProvider()->get('\PM\Profiler\Service\Test');
        /* @var $testService \PM\Profiler\Service\Test */

        $this->getExecutor()
            ->add('getTest', $testService, array('testId' => $testId));
    }

    /**
     * Delete test of MySQL scenario by given id.
     *
     * @link /mysql/delete/{testId}
     *
     * @method DELETE
     *
     * @param int $testId Test ID of MySQL scenario
     *
     * @return void
     */
    public function actionMySQLDeleteTest($testId) {
        $testService = $this->getProvider()->get('\PM\Profiler\Service\Test');
        /* @var $testService \PM\Profiler\Service\Test */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('deleteTest', $testService, array('testId' => $testId))
            ->add(function(Database $database, $data) {
                $database->getTransaction()->commitAll();
                return $data;
            });
    }

    /**
     * This find all measures.
     *
     * @link /mysql/{testId}/measure/get
     *
     * @method GET
     *
     * @param int $testId Test ID of MySQL scenario
     *
     * @return void
     */
    public function actionMySQLTestMeasures($testId) {
        $measureService = $this->getProvider()->get('\PM\Profiler\Service\Measure');
        /* @var $measureService \PM\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('findMeasuresForTest', $measureService, array('testId' => $testId));
    }
}
