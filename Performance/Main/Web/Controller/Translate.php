<?php

/**
 * This scripts defines class for translate controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @link /translate
 */
class Performance_Main_Web_Controller_Translate extends Performance_Main_Web_Controller_Abstract_Json {

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
        $config             = $this->getProvider()->get('config')->get('translate'); /* @var $config Performance_Main_Config */
        // sets default lang from configuration
        $this->_defaultLang = isset($config['lang']) ? $config['lang'] : Performance_Main_Translate_Enum_Lang::ENGLISH;
        // loading cache data
        $cache              = $this->getProvider()->get('cache'); /* @var $cache Performance_Main_Cache */
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
        $langs = Performance_Main_Translate_Enum_Lang::getConstants();
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
     * @param enum $module One of Performance_Main_Translate_Enum_Module
     * @param enum $lang   One of Performance_Main_Translate_Enum_Lang
     *
     * @return array
     */
    private function _getTranslation($module, $lang) {
        $repository = $this->getProvider()->get('Performance_Main_Translate_Repository'); /* @var $repository Performance_Main_Translate_Repository */
        $translateTable = $repository->getTranslateTable($lang, $module);
        return array(
            'lang'      => $lang,
            'translate' => $translateTable
        );
    }
}
