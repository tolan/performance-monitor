<?php

namespace PM\Translate\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;

/**
 * This scripts defines abstract class for translate controllers.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Translate
 *
 * @link /translate/language
 */
class AbstractController extends Json {

    /**
     * Returns acutal selected language.
     *
     * @return string
     */
    protected function getActualLang() {
        $config = $this->getProvider()->get('config')->get('translate'); /* @var $config \PM\Main\Config */
        // sets default lang from configuration
        $lang = isset($config['lang']) ? $config['lang'] : Lang::ENGLISH;
        // loading cache data
        $cache = $this->getProvider()->get('cache'); /* @var $cache \PM\Main\Cache */
        // When cache contains language then it overwrite actual language
        $lang = $cache->has('lang') ? $cache->load('lang') : $lang;

        return $lang;
    }
}
