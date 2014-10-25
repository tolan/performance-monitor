<?php

namespace PM\Main\Web\View;

use PM\Main\Abstracts\Entity;

/**
 * Abstract class for each JSON view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Json extends AbstractView {

    /**
     * Generates payload.
     *
     * @return mixed
     */
    public function getPayload() {
        $data          = $this->getData();
        $extractedData = $this->_extractData($data);

        return $extractedData;
    }

    /**
     * Extracts data for json.
     *
     * @param mixed $data Data to extracct
     *
     * @return mixed
     */
    private function _extractData($data) {
        if (is_array($data) || $data instanceof \Iterator) {
            foreach ($data as $key => $item) {
                $data[$key] = $this->_extractData($item);
            }
        } elseif ($data instanceof Entity) {
            $data = $data->toArray(true);
        }

        return $data;
    }

    /**
     * Returns JSON template.
     *
     * @return \PM\Main\Web\Component\Template\Json
     */
    final protected function getTemplate() {
        return parent::getTemplate();
    }
}
