<?php

namespace PM\Translate\Web\Controller;

/**
 * This scripts defines class of translate controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Translate
 *
 * @link /translate/module
 */
class Module extends AbstractController {

    /**
     * Gets transalte table for module.
     *
     * @link /{module}
     *
     * @method GET
     *
     * @param enum $module One of \PM\Translate\Enum\Module
     *
     * @return void
     */
    public function actionTranslate($module) {
        $this->setData($this->_getTranslation($module, $this->getActualLang()));
    }

    /**
     * Gets transalte table for module.
     *
     * @link /{module}/{lang}
     *
     * @method GET
     *
     * @param enum $module One of enum \PM\Translate\Enum\Module
     * @param enum $lang   One of enum \PM\Translate\Enum\Lang
     *
     * @return void
     */
    public function actionTranslateByLang($module, $lang) {
        // sets selected language to cache
        $this->getProvider()->get('cache')->save('lang', $lang);

        $this->setData($this->_getTranslation($module, $lang));
    }

    /**
     * Returns transalte table by given module and language.
     *
     * @param enum $module One of \PM\Translate\Enum\Module
     * @param enum $lang   One of \PM\Translate\Enum\Lang
     *
     * @return array
     */
    private function _getTranslation($module, $lang) {
        $repository     = $this->getProvider()->get('PM\Translate\Repository\Translation'); /* @var $repository \PM\Translate\Repository\Translation */
        $translateTable = $repository->getTranslateTable($lang, $module);

        return array(
            'lang'      => $lang,
            'translate' => $translateTable
        );
    }
}
