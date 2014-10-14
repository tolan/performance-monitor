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
        $repository = $this->getProvider()->get('PF\Main\Navigation\Repository'); /* @var $repository \PF\Main\Navigation\Repository */
        $convertor  = $this->getProvider()->prototype('PF\Main\Tree\Convertor'); /* @var $convertor \PF\Main\Tree\Convertor */
        $config     = $this->getProvider()->prototype('PF\Main\Tree\Config'); /* @var $config \PF\Main\Tree\Config */
        $config->setChildrenIdentificator('submenu');

        $this->getExecutor()->add('getMenuItems', $repository)
                ->add(function($data) {
                    return array('list' => $data);
                })
                ->add('convert', $convertor, array('config' => $config));
    }
}
