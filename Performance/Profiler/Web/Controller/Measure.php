<?php

namespace PM\Profiler\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;

/**
 * This scripts defines class of profiler measure controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 *
 * @link /profiler/measure
 */
class Measure extends Json {

    /**
     * Returns list of measures stored in files.
     *
     * @link /file/get
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetFileMeasures() {
        $measureService = $this->getProvider()->get('\PM\Profiler\Service\Measure');
        /* @var $measureSerice \PM\Profiler\Service\Measure */

        $this->getExecutor()->add('findFileMeasures', $measureService);
    }

    /**
     * Delete action of file with measure.
     *
     * @link /file/delete/{id}
     *
     * @method DELETE
     *
     * @param string $id Measure identification
     *
     * @return void
     */
    public function actionDeleteFileMeasure($id) {
        $measureService = $this->getProvider()->get('\PM\Profiler\Service\Measure');
        /* @var $measureSerice \PM\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('deleteFileMeasure', $measureService, array('measureId' => $id));
    }

    /**
     * Gets statistics for measure by measure id.
     *
     * @link /{type}/{id}/summary
     *
     * @method GET
     *
     * @session_write_close false
     *
     * @param enum $type One of enum \PM\Profiler\Monitor\Enum\Type
     * @param int  $id   Measure ID
     *
     * @return void
     */
    public function actionGetMeasureStatistic($type, $id) {
        $measureService = $this->getProvider()->get('\PM\Profiler\Service\Measure');
        /* @var $measureSerice \PM\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('getMeasureStatistics', $measureService, array('type' => $type, 'measureId' => $id));

        return $this->getExecutor()->execute()
            ->reset('input')
            ->reset('type')
            ->reset('measureId')
            ->toArray();
    }

    /**
     * Gets calls stack for measure by measure id and parent call id (zero means all root calls)
     *
     * @link /{type}/{id}/callStack/parent/{parentId}
     *
     * @method GET
     *
     * @session_write_close false
     *
     * @param enum $type     One of enum \PM\Profiler\Monitor\Enum\Type
     * @param int  $id       Measure ID
     * @param int  $parentId ID of parent call
     *
     * @return void
     */
    public function actionGetMeasureCallStack($type, $id, $parentId) {
        $measureService = $this->getProvider()->get('\PM\Profiler\Service\Measure');
        /* @var $measureSerice \PM\Profiler\Service\Measure */

        $data = array(
            'type'      => $type,
            'measureId' =>  $id,
            'parentId'  => $parentId
        );

        $this->getExecutor()
            ->add('getMeasureCallStack', $measureService, $data);
    }

    /**
     * Gets function statistics for measure by measure id.
     *
     * @link /{type}/{id}/statistic/function
     *
     * @method GET
     *
     * @session_write_close false
     *
     * @param enum $type     One of enum \PM\Profiler\Monitor\Enum\Type
     * @param int  $id       Measure ID
     *
     * @return void
     */
    public function actionGetMeasureCallsStatistic($type, $id) {
        $measureService = $this->getProvider()->get('\PM\Profiler\Service\Measure');
        /* @var $measureSerice \PM\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('getMeasureCallsStatistic', $measureService, array('type' => $type, 'measureId' => $id));
    }
}
