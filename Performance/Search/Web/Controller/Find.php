<?php

namespace PM\Search\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;

/**
 * This scripts defines class of search find controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 *
 * @link /search/find
 */
class Find extends Json {

    /**
     * It provides entry point for finding entities by given filters and target entity.
     * It returns a list of matching entities.
     * Request method is POST because method GET has not body as POST for sets filters.
     *
     * @link /entity/{entity}
     *
     * @method POST
     *
     * @param enum $entity One of enum \PM\Search\Enum\Target
     *
     * @return void
     */
    public function actionFind($entity) {
        $data   = $this->getRequest()->getInput();
        $result = $this->getProvider()->get('PM\Search\Engine')->find($data['template']);

        $this->setData(array(
            'target' => $entity,
            'result' => $result
        ));
    }
}
