<?php

namespace PF\Main\Web\Controller;

/**
 * This scripts defines class for translate controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @link /translate
 */
class Translate extends Abstracts\Json {

    /**
     * Default lang.
     *
     * @var string
     */
    private $_defaultLang;

    /**
     * Init method sets basic language information.
     *
     * @return void
     */
    protected function init() {
        $config             = $this->getProvider()->get('config')->get('translate'); /* @var $config \PF\Main\Config */
        // sets default lang from configuration
        $this->_defaultLang = isset($config['lang']) ? $config['lang'] : \PF\Main\Translate\Enum\Lang::ENGLISH;
        // loading cache data
        $cache              = $this->getProvider()->get('cache'); /* @var $cache \PF\Main\Cache */
        // When cache contains language then it overwrite actual language
        $this->_defaultLang = $cache->has('lang') ? $cache->load('lang') : $this->_defaultLang;

        parent::init();
    }

    /**
     * Gets transalte table for module.
     *
     * @link /module/{module}
     *
     * @method GET
     *
     * @return void
     */
    public function actionTranslate($params) {
        $this->setData($this->_getTranslation($params['module'], $this->_defaultLang));
    }

    /**
     * Gets transalte table for module.
     *
     * @link /module/{module}/{lang}
     *
     * @method GET
     *
     * @return void
     */
    public function actionTranslateByLang($params) {
        // sets selected language to cache
        $this->getProvider()->get('cache')->save('lang', $params['lang']);

        $this->setData($this->_getTranslation($params['module'], $params['lang']));
    }

    /**
     * Returns all supported langs.
     *
     * @link /langs
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetLangs() {
        $langs = \PF\Main\Translate\Enum\Lang::getConstants();
        $result = array();

        foreach ($langs as $lang) {
            $result[] = array(
                'value' => $lang,
                'name' => 'main.language.'.strtolower($lang)
            );
        }

        $this->setData(
            array(
                'default' => $this->_defaultLang,
                'langs'   => $result
            )
        );
    }

    /**
     * Returns transalte table by given module and language.
     *
     * @param enum $module One of \PF\Main\Translate\Enum\Module
     * @param enum $lang   One of \PF\Main\Translate\Enum\Lang
     *
     * @return array
     */
    private function _getTranslation($module, $lang) {
        $repository     = $this->getProvider()->get('PF\Main\Translate\Repository'); /* @var $repository \PF\Main\Translate\Repository */
        $translateTable = $repository->getTranslateTable($lang, $module);
        return array(
            'lang'      => $lang,
            'translate' => $translateTable
        );
    }
}
