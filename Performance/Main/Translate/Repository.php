<?php

/**
 * This script defines repository class for translation texts.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Translate_Repository extends Performance_Main_Abstract_Repository {

    /**
     * Returns translate table by given language and module.
     *
     * @param enum $lang   One of Performance_Main_Translate_Enum_Lang
     * @param enum $module One of Performance_Main_Translate_Enum_Module
     *
     * @return array
     */
    public function getTranslateTable($lang = Performance_Main_Translate_Enum_Lang::ENGLISH, $module = null) {
        $select = $this->getDatabase()
                ->select()
                ->columns(array('`key`' => 'CONCAT(t.module, ".", t.key)'))
                ->from(array('t' => 'translate'), array('text'))
                ->where('t.lang = ?', $lang);

        if ($module) {
            $select->where('t.module IN (?)', $module);
        }

        $data = $select->fetchAll();
        $result = array();

        foreach ($data as $item) {
            $result[$item['key']] = $item['text'];
        }

        return $result;
    }
}
