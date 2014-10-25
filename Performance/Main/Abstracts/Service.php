<?php

namespace PM\Main\Abstracts;

use PM\Main\Commander;

/**
 * Abstract class for service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Service {

    /**
     * Commander instance.
     *
     * @var \PM\Main\Commander
     */
    private $_commander;

    /**
     * Construct method.
     *
     * @param \PM\Main\Commander $commander Commander instance
     */
    final public function __construct(Commander $commander) {
        $this->_commander = $commander;

        $this->init();
    }

    /**
     * Returns instance of executor by given name.
     *
     * @param string $name Identificator name for executor [optional]
     *
     * @return \PM\Main\Commander\Executor
     */
    final protected function getExecutor($name = null) {
        if ($name === null) {
            $name = uniqid();
        }

        return $this->_commander->getExecutor('service_'.$name);
    }

    /**
     * Optional init function for prepare attributes
     *
     * @return \PM\Main\Abstracts\Service
     */
    protected function init() {}

    /**
     * Helper method for update sub-entities referenced to updating entity.
     * In option entity must set following attributes:
     *      subEntityName                  - Name of sub-entities
     *      parentIdParameter              - Parameter of sub-entity which defined parend ID
     *      createFunction                 - Name of function which is called for creating new sub-entities
     *      createFunctionDataParameter    - Name of data parameter of function "createFunction"
     *      updateFunction                 - Name of function which is called for updating existed sub-entities
     *      updateFunctionDataParameter    - Name of data parameter of function "updateFunction"
     *      updateFunctionOldDataParameter - Name of data parameter which has means existed sub-entity of function "updateFunction"
     *      deleteFunction                 - Name of function for delete existed sub-entities
     *      deleteFunctionDataParameter    - Name of data parameter of function "deleteFunction" [optional]
     *
     * @param \PM\Main\Abstracts\Entity  $upEntity      Instance of entity which will be updated and has sub-entities
     * @param \PM\Main\Abstracts\Entity  $oldEntity     Instance of existed entity which has existed sub-entities
     * @param \PM\Main\Abstracts\Service $entityService Service which provide all functions for create, update and delete sub-entities
     * @param \PM\Main\Abstracts\Entity  $options       Instance of option entity for configuration of this method
     *
     * @return \PM\Main\Abstracts\Entity Returns updated entity
     */
    protected function updateSubEntities(Entity $upEntity, Entity $oldEntity, Service $entityService, Entity $options) {
        $existed                = array();
        $subEntityName          = $options->get('subEntityName');
        $parentIdParam          = $options->get('parentIdParameter');
        $createFunction         = $options->get('createFunction');
        $createFuncDataParam    = $options->get('createFunctionDataParameter');
        $updateFunction         = $options->get('updateFunction');
        $updateFuncDataParam    = $options->get('updateFunctionDataParameter');
        $updateFuncOldDataParam = $options->get('updateFunctionOldDataParameter', false);
        $deleteFunction         = $options->get('deleteFunction');
        $deleteFuncDataParam    = $options->get('deleteFunctionDataParameter', 'id');

        foreach ($oldEntity->get($subEntityName) as $subEntity) {
            $existed[$subEntity->getId()] = $subEntity;
        }

        $toCreate = array();
        $toUpdate = array();

        foreach ($upEntity->get($subEntityName) as $subEntity) {
            if (array_key_exists('id', $subEntity) === false || array_key_exists($subEntity['id'], $existed) === false) {
                $toCreate[] = $subEntity;
            } elseif(array_key_exists($subEntity['id'], $existed) === true) {
                $toUpdate[$subEntity['id']] = $subEntity;
            }
        }

        $toDelete = array_diff_key($existed, $toUpdate);

        $executor = $this->getExecutor()->clean()->add($createFunction, $entityService);
        foreach ($toCreate as $subEntity) {
            $subEntity[$parentIdParam] = $upEntity->getId();
            $executor->getResult()->set($createFuncDataParam, $subEntity);
            $executor->execute();
        }

        $executor->clean()->add($updateFunction, $entityService);
        foreach ($toUpdate as $id => $subEntity) {
            $executor->getResult()->set($updateFuncDataParam, $subEntity);
            if ($updateFuncOldDataParam !== false && array_key_exists($id, $existed)) {
                $executor->getResult()->set($updateFuncOldDataParam, $existed[$id]);
            }

            $executor->execute();
        }

        $executor->clean()->add($deleteFunction, $entityService);
        foreach (array_keys($toDelete) as $subEntityId) {
            $executor->getResult()->set($deleteFuncDataParam, $subEntityId);
            $executor->execute();
        }

        return $upEntity;
    }
}
