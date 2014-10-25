<?php

namespace PM\Translate\Repository;

use PM\Main\Abstracts\Repository;
use PM\Translate\Enum\Lang;

/**
 * This script defines repository class for translation texts.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Translate
 */
class Translation extends Repository {

    /**
     * Returns translate table by given language and module.
     *
     * @param enum $lang   One of \PM\Translate\Enum\Lang
     * @param enum $module One of \PM\Translate\Enum\Module
     *
     * @return array
     */
    public function getTranslateTable($lang = Lang::ENGLISH, $module = null) {
        $select = $this->getDatabase()
                ->select()
                ->columns(array('`key`' => 'CONCAT(t.module, ".", t.key)'))
                ->from(array('t' => 'translate'), array('text'))
                ->where('t.lang = :lang', array(':lang' => $lang));

        if ($module) {
            $select->where('t.module IN (:module)', array(':module' => $module));
        }

        $data = $select->fetchAll();
        $result = array();

        foreach ($data as $item) {
            $result[$item['key']] = $item['text'];
        }

        return $result;
    }
}
