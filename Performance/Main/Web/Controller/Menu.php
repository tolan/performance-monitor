<?php

namespace PF\Main\Web\Controller;

/**
 * This scripts defines class for menu controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Menu extends Abstracts\Json {

    /**
     * Index action for get menu structure.
     *
     * @return void
     */
    public function actionIndex() {
        $menu = array(
            array(
                'text'    => 'main.menu.summary',
                'submenu' => array(
                    array('text' => 'main.menu.summary.mysql', 'href' => '#profiler/mysql/scenarios'),
                    array('text' => 'main.menu.summary.file', 'href' => '#profiler/file/list')
                )
            ),
            array('text' => 'main.menu.search', 'href' => '#search'),
            array('text' => 'main.menu.statistics', 'href' => '#statistics'),
            array('text' => 'main.menu.optimalization', 'href' => '#measure/optimalization'),
            array(
                'text'    => 'main.menu.setup',
                'submenu' => array(
                    array('text' => 'main.menu.cron', 'href' => '#/settings/cron'),
                    array('text' => 'main.menu.about', 'href' => '#/settings/about')
                )
            )
        );

        $this->setData($menu);
    }
}
