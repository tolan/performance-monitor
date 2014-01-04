<?php

/**
 * This scripts defines class for menu controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Controller_Menu extends Performance_Main_Web_Controller_Abstract_Json {

    /**
     * Index action for get menu structure.
     *
     * @return void
     */
    public function actionIndex() {
        $menu = array(
            array(
                'text' => 'main.menu.summary',
                'href' => '#profiler/list'
            ),
            array(
                'text' => 'main.menu.search',
                'href' => '#search'
            ),
            array(
                'text' => 'main.menu.statistics',
                'href' => '#measure/statistics'
            ),
            array(
                'text' => 'main.menu.optimalization',
                'href' => '#measure/optimalization'
            ),
            array(
                'text' => 'main.menu.setup',
                'submenu' => array(
                    array(
                        'text' => 'main.menu.cron',
                        'href' => '#/settings/cron'
                    ),
                    array(
                        'text' => 'main.menu.about',
                        'href' => '#/settings/about'
                    ),
                )
            )
        );
        $this->setData($menu);
    }
}
