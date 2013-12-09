<?php

/**
 * This scripts defines class for translate controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 */
class Performance_Main_Web_Controller_Translate extends Performance_Main_Web_Controller_Abstract_Json {

    /**
     * Index action. Sets to data translate table.
     *
     * @return void
     */
    public function actionIndex() {
        $translateTable = array(
            'main.validator.required'  => 'Pole musí být vyplněno!',
            'main.validator.minLength' => 'Minimální délka je #MIN_LENGTH znaků.',
            'main.validator.password'  => 'Heslo není validní (musí obsahovat malá a velká písmena a číslice).',
            'main.validator.regex'     => 'Nevyhovuje požadavkům: #REGEX.'
        );
        $this->setData($translateTable);
    }
}
