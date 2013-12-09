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
                'text' => 'Přehled',
                'href' => '#profiler/list'
            ),
            array(
                'text' => 'Vyhledávání',
                'href' => '#measure/search'
            ),
            array(
                'text' => 'Statistiky',
                'href' => '#measure/statistics'
            ),
            array(
                'text' => 'Optimalizace',
                'href' => '#measure/optimalization'
            ),
            array(
                'text' => 'Nastavení',
                'submenu' => array(
                    array(
                        'text' => 'Plánovač',
                        'href' => '#/settings/cron'
                    ),
                    array(
                        'text' => 'O aplikaci',
                        'href' => '#/settings/about'
                    ),
                )
            )
        );
        $this->setData($menu);
    }
}
