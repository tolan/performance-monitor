<?php

namespace PM\Main\Web\Controller;

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
        $repository = $this->getProvider()->get('PM\Main\Navigation\Repository'); /* @var $repository \PM\Main\Navigation\Repository */
        $convertor  = $this->getProvider()->prototype('PM\Main\Tree\Convertor'); /* @var $convertor \PM\Main\Tree\Convertor */
        $config     = $this->getProvider()->prototype('PM\Main\Tree\Config'); /* @var $config \PM\Main\Tree\Config */
        $config->setChildrenIdentificator('submenu');

        $this->getExecutor()->add('getMenuItems', $repository)
                ->add(function($data) {
                    return array('list' => $data);
                })
                ->add('convert', $convertor, array('config' => $config));
    }
}
