<?php

namespace PM\Statistic\Service;

use PM\Statistic\Repository;
use PM\Statistic\Entity;
use PM\Statistic\Enum;
use PM\Search;
use PM\Main\Abstracts\Service;
use PM\Main\CommonEntity;

/**
 * This script defines class for template service of statistic template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Template extends Service {

    /**
     * Returns list of statistic template entities.
     *
     * @param \PM\Statistic\Repository\Template $repository Template repository instance
     *
     * @return \PM\Statistic\Entity\Template[]
     */
    public function findTemplates(Repository\Template $repository) {
        $data = $repository->findTemplates();

        foreach ($data as $temaplate) {
            $temaplate->reset('sourceType');
            $temaplate->reset('sourceTemplateId');
        }

        return $data;
    }

    /**
     * Returns statistic template entity with source and assigned views.
     *
     * @param int                               $id            ID of statistic template
     * @param \PM\Statistic\Repository\Template $repository    Template repository instance
     * @param \PM\Statistic\Service\View        $viewService   View service instance
     * @param \PM\Search\Service\Template       $searchService Search service instance
     *
     * @return \PM\Statistic\Entity\Template
     */
    public function getTemplate($id, Repository\Template $repository, View $viewService, Search\Service\Template $searchService) {
        $template = $repository->getTemplate($id);

        $this->_setSource($template, $repository, $searchService);
        $this->_setViews($template, $viewService);

        return $template;
    }

    /**
     * Helper method to obtain and set source of statistic template entity.
     *
     * @param \PM\Statistic\Entity\Template     $template      Statistic template entity instance
     * @param \PM\Statistic\Repository\Template $repository    Template repository instance
     * @param \PM\Search\Service\Template       $searchService Search service instance
     *
     * @return \PM\Statistic\Entity\Template
     */
    private function _setSource(Entity\Template $template, Repository\Template $repository, Search\Service\Template $searchService) {
        $searchTemplateId = $template->get('sourceTemplateId');
        $sourceType       = $template->get('sourceType');
        $template->reset('sourceTemplateId')->reset('sourceType');

        $executor = $this->getExecutor()->add('getTemplate', $searchService);
        $executor->getResult()->setId($searchTemplateId);
        $searchTemplate = $executor->execute()->getData();

        $items = $this->_getItems($template->getId(), $sourceType, $repository);

        $source = array(
            'type'     => $sourceType,
            'template' => $searchTemplate->toArray(),
            'items'    => $items,
            'target'   => $searchTemplate->getTarget()
        );

        return $template->setSource($source);
    }

    /**
     * Helper method to get assigned items of statistic template entity.
     *
     * @param int                               $templateId ID of statistic template
     * @param enum                              $sourceType One of enum \PM\Statistic\Enum\Source\Type
     * @param \PM\Statistic\Repository\Template $repository Template repository instance
     *
     * @return array
     */
    private function _getItems($templateId, $sourceType, Repository\Template $repository) {
        $items = array();
        if ($sourceType !== Enum\Source\Type::TEMPLATE) {
            $items = $repository->getItemsForTemplate($templateId);
        }

        return $items;
    }

    /**
     * Helper method to obtain and set assigned views of statistic template entity.
     *
     * @param \PM\Statistic\Entity\Template $template    Statistic template instance
     * @param \PM\Statistic\Service\View    $viewService View service instance
     *
     * @return \PM\Statistic\Entity\Template
     */
    private function _setViews(Entity\Template $template, View $viewService) {
        $executor = $this->getExecutor()->add('getViewsForTemplate', $viewService);
        $executor->getResult()->setTemplateId($template->getId());
        $views = $executor->execute()->getData();

        return $template->setViews($views);
    }

    /**
     * Creates new statistic template entity.
     *
     * @param array                             $templateData  Data of statistic template for create
     * @param \PM\Statistic\Repository\Template $repository    Template repository instance
     * @param \PM\Statistic\Service\View        $viewService   View service instance
     * @param \PM\Search\Service\Template       $searchService Search service instance
     *
     * @return \PM\Statistic\Entity\Template
     */
    public function createTemplate($templateData, Repository\Template $repository, View $viewService, Search\Service\Template $searchService) {
        $template = new Entity\Template($templateData);

        $source         = $template->get('source');
        $searchTemplate = $source['template'];

        if (!isset($searchTemplate['id'])) {
            $executor = $this->getExecutor()->add('createTemplate', $searchService);
            $searchTemplate['visible'] = false;
            $searchTemplate['usage']   = Search\Enum\Usage::STATISTIC;
            $searchTemplate['name']    = $template->getName();
            $executor->getResult()->set('templateData', $searchTemplate);
            $searchTemplate['id'] = $executor->execute()->get('data');
        }

        $source['template'] = $searchTemplate;
        $template->set('source', $source);

        $repository->createTemplate($template);

        if ($source['type'] !== Enum\Source\Type::TEMPLATE) {
            $repository->assignItemsToTemplate($template->getId(), $source['items']);
        }

        $executor = $this->getExecutor()->add('createView', $viewService);

        $views = array();
        foreach ($template->getViews() as $view) {
            $view['templateId'] = $template->getId();
            $executor->getResult()->set('viewData', $view);
            $views[] = $executor->execute()->getData();
        }

        $template->setViews($views);

        return $template->getId();
    }

    /**
     * Updates existed statistic template entity.
     *
     * @param array                             $templateData  Data of statistic template
     * @param \PM\Statistic\Repository\Template $repository    Template repository instance
     * @param \PM\Statistic\Service\View        $viewService   View service instance
     * @param \PM\Search\Service\Template       $searchService Search service instance
     *
     * @return \PM\Statistic\Entity\Template
     */
    public function updateTemplate($templateData, Repository\Template $repository, View $viewService, Search\Service\Template $searchService) {
        $template = new Entity\Template($templateData);

        $source         = $template->get('source');
        $searchTemplate = $source['template'];

        if (!isset($searchTemplate['id'])) {
            $executor = $this->getExecutor()->add('createTemplate', $searchService);
            $searchTemplate['visible'] = false;
            $searchTemplate['usage']   = Search\Enum\Usage::STATISTIC;
            $searchTemplate['name']    = $template->getName();
            $executor->getResult()->set('templateData', $searchTemplate);
            $searchTemplate['id'] = $executor->execute()->get('data');
        }

        $source['template'] = $searchTemplate;
        $template->set('source', $source);

        $repository->updateTemplate($template);

        if ($source['type'] !== Enum\Source\Type::TEMPLATE) {
            $repository->deleteItemsForTemplate($template->getId());
            $repository->assignItemsToTemplate($template->getId(), $source['items']);
        }

        $oldEntity = $this->getTemplate($template->getId(), $repository, $viewService, $searchService);
        $options = new CommonEntity(
            array(
                'subEntityName'                  => 'views',
                'parentIdParameter'              => 'templateId',
                'createFunction'                 => 'createView',
                'createFunctionDataParameter'    => 'viewData',
                'updateFunction'                 => 'updateView',
                'updateFunctionDataParameter'    => 'viewData',
                'updateFunctionOldDataParameter' => 'oldView',
                'deleteFunction'                 => 'deleteView'
            )
        );

        $this->updateSubEntities($template, $oldEntity, $viewService, $options);

        return $template;
    }

    /**
     * Deletes statistic template entity by given ID.
     *
     * @param int                               $id            ID of statistic template entity
     * @param \PM\Statistic\Repository\Template $repository    Template repository instance
     * @param \PM\Statistic\Service\View        $viewService   View service instance
     * @param \PM\Search\Service\Template       $searchService Search service instance (optional)
     *
     * @return int
     */
    public function deleteTemplate($id, Repository\Template $repository, View $viewService, Search\Service\Template $searchService) {
        $template = $this->getTemplate($id, $repository, $viewService, $searchService);

        $searchTemplate = $template->getSource()['template'];

        if ($searchTemplate['usage'] === Search\Enum\Usage::STATISTIC && $searchTemplate['visible'] === false) {
            $executor = $this->getExecutor()->add('deleteTemplate', $searchService);
            $executor->getResult()->setId($searchTemplate['id']);
            $executor->execute();
        }

        return $repository->deleteTemplate($id);
    }
}
