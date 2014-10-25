<?php

namespace PM\Translate\Web\Controller;

use PM\Translate\Enum\Lang;

/**
 * This scripts defines class of language controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Translate
 *
 * @link /translate/language
 */
class Language extends AbstractController {

    /**
     * Returns all supported langs.
     *
     * @link /get
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetLangs() {
        $langs = Lang::getConstants();
        $result = array();

        foreach ($langs as $lang) {
            $result[] = array(
                'value' => $lang,
                'name'  => 'main.language.'.strtolower($lang)
            );
        }

        $this->setData(
            array(
                'default' => $this->getActualLang(),
                'langs'   => $result
            )
        );
    }
}
