<?php

namespace PM\Statistic\Service;

use PM\Main\Abstracts\Service;
use PM\Statistic\Repository;
use PM\Statistic\Entity;
use PM\Main\CommonEntity;

/**
 * This script defines class for view service of statistic template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class View extends Service {

    /**
     * Returns view entities for statistic template by given template ID.
     *
     * @param int                           $templateId  ID of statistic template entity
     * @param \PM\Statistic\Repository\View $repository  View repository instance
     * @param \PM\Statistic\Service\Line    $lineService Line service instance
     *
     * @return \PM\Statistic\Entity\View[]
     */
    public function getViewsForTemplate($templateId, Repository\View $repository, Line $lineService) {
        $views = $repository->getViewsForTemplate($templateId);

        $ids = array();
        foreach ($views as $key => $view) {
            $ids[$view->getId()] = $key;
        }

        $executor = $this->getExecutor()->add('getLinesForViews', $lineService);
        $executor->getResult()->set('viewIds', array_keys($ids));
        $lines = $executor->execute()->getData();

        foreach ($lines as $line) {
            $viewId  = $line->getViewId();
            $viewKey = $ids[$viewId];
            $view    = $views[$viewKey];

            $lines   = $view->get('lines', array());
            $lines[] = $line;

            $view->set('lines', $lines);
        }

        return $views;
    }

    /**
     * Creates new view entity.
     *
     * @param array                         $viewData    Data of view for create
     * @param \PM\Statistic\Repository\View $repository  View repository instance
     * @param \PM\Statistic\Service\Line    $lineService Line service instance
     *
     * @return \PM\Statistic\Entity\View
     */
    public function createView($viewData, Repository\View $repository, Line $lineService) {
        $view = new Entity\View($viewData);

        $repository->createView($view);

        $executor = $this->getExecutor()->add('createLine', $lineService);

        $lines = array();
        foreach ($view->getLines() as $line) {
            $line['viewId'] = $view->getId();
            $executor->getResult()->set('lineData', $line);
            $lines[] = $executor->execute()->getData();
        }

        $view->setLines($lines);

        return $view->getId();
    }

    /**
     * Updates existed view entity.
     *
     * @param array                         $viewData    Data of view entity
     * @param \PM\Statistic\Repository\View $repository  View repository instance
     * @param \PM\Statistic\Service\Line    $lineService Line service instance
     * @param \PM\Statistic\Entity\View     $oldView     Existed view entity (optional)
     *
     * @return \PM\Statistic\Entity\View
     */
    public function updateView($viewData, Repository\View $repository, Line $lineService, Entity\View $oldView = null) {
        $view = new Entity\View($viewData);

        $repository->updateView($view);

        if ($oldView === null) {
            $oldView = $this->getView($view->getId());
        }

        $options = new CommonEntity(
            array(
                'subEntityName'               => 'lines',
                'parentIdParameter'           => 'viewId',
                'createFunction'              => 'createLine',
                'createFunctionDataParameter' => 'lineData',
                'updateFunction'              => 'updateLine',
                'updateFunctionDataParameter' => 'lineData',
                'deleteFunction'              => 'deleteLine'
            )
        );

        $this->updateSubEntities($view, $oldView, $lineService, $options);

        return $view;
    }

    /**
     * Deletes view entity by given ID.
     *
     * @param int                           $id         ID of view entity
     * @param \PM\Statistic\Repository\View $repository View repository instance
     *
     * @return int
     */
    public function deleteView($id, Repository\View $repository) {
        return $repository->deleteView($id);
    }
}
