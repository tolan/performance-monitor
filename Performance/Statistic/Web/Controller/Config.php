<?php

namespace PM\Statistic\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Commander\Result;

/**
 * This scripts defines class of statistic config controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 *
 * @link /statistic/config
 */
class Config extends Json {

    /**
     * Gets configuration and references for menus, line - type map, etc.
     *
     * @link /views
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetConfigViews() {
        $associtaion = $this->getProvider()->get('\PM\Statistic\Association'); /* @var $associtaion \PM\Statistic\Association */

        $this->getExecutor()
            ->add('getConfig', $associtaion)
            ->add(function(Result $result) {
                $data = $result->toArray();
                unset($data['input']);

                return array('data' => $data);
            });
    }
}
