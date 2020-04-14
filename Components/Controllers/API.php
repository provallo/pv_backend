<?php

namespace ProVallo\Plugins\Backend\Components\Controllers;

use Favez\ORM\Entity\Entity;

/**
 * Class API
 *
 * @package ProVallo\Plugins\Backend\Components\Controllers
 *
 * @method \ProVallo\Plugins\Backend\Components\Auth auth()
 */
abstract class API extends \ProVallo\Components\Controller
{
    const DEFAULT_PERMISSION = 'user.backend.access';

    const DEFAULT_CONFIG     = [
        'model'          => '',
        'allowedActions' => [],
        'detail'         => [
            'recursive' => false,
        ],
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * See API::DEFAULT_CONFIG
     *
     * @return array
     */
    abstract public function configure();

    /**
     * Make sure the user is logged in before he grants access to api
     * controllers.
     */
    final public function preDispatch()
    {
        $this->config = $this->configure();
        $this->config = array_replace_recursive(self::DEFAULT_CONFIG, $this->config);

        if (!$this->isLoggedIn()) {
            $this->app()->respond(self::json()->failure([ 'message' => 'Not logged in' ]));
            die;
        }

        if (
            $this->isLoggedIn()
            && !empty(static::DEFAULT_PERMISSION)
            && !self::permission()->validateUserID(
                static::DEFAULT_PERMISSION,
                self::auth()->getUserID()
            )
        ) {
            $this->app()->respond(
                self::json()->failure(
                    [
                        'message' => 'You are not allowed to enter this area.',
                    ]
                )
            );
            die;
        }
    }

    /**
     * Whether the user is authenticated.
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        if (self::auth()->isLoggedIn()) {
            return true;
        }

        if (in_array(self::dispatcher()->actionName(), $this->config['allowedActions'] ?? [])) {
            return true;
        }

        return false;
    }

    final public function listAction()
    {
        $rows = $this->getListQuery()->fetchAll();
        $rows = array_map(
            [
                $this,
                'map',
            ],
            $rows
        );

        return self::json()->success(
            [
                'data' => $rows,
            ]
        );
    }

    public function saveAction()
    {
        $input = self::request()->getParams();
        $id    = (int)self::request()->getParam('id');
        $isNew = $id <= 0;

        /** @var \Favez\ORM\Entity\Repository $repository */
        $repository = $this->getClass()::repository();
        $className  = $this->getClass();

        if (!$isNew) {
            $model = $repository->find($id);

            if (!($model instanceof $className)) {
                return self::json()->failure([ 'message' => 'Entity by id not found.' ]);
            }

            if (!$this->checkPermission($model, 'save')) {
                return self::json()->failure([ 'message' => 'You are not permitted to edit this entity.' ]);
            }
        } else {
            $model = $repository->create();
            $this->setDefaultValues($model);
        }

        $result = $this->setValues($model, $input);

        if ($result && !isSuccess($result)) {
            return self::json()->failure(
                [
                    'message' => $result['message'],
                ]
            );
        }

        /** @var \ProVallo\Plugins\Backend\Components\ModelValidator $validator */
        $validator = self::modelValidator();

        if ($validator->validate($model)) {
            $model->save();

            $this->afterSave($model, $isNew);

            return self::json()->success(
                [
                    'data' => $model->toArray($this->config['detail']['recursive']),
                ]
            );
        }

        return self::json()->failure(
            [
                'messages' => $validator->getMessages(),
            ]
        );
    }

    public function detailAction()
    {
        $id = (int)self::request()->getParam('id');

        try {
            if ($id <= 0) {
                throw new Exception('Missing required parameter: id');
            }

            /** @var \Favez\ORM\Entity\Repository $repository */
            $repository = $this->getClass()::repository();
            $className  = $this->getClass();
            $model      = $repository->find($id);

            if ($model instanceof $className) {
                if ($this->checkPermission($model, 'detail')) {
                    return self::json()->success(
                        [
                            'data' => $model->toArray($this->config['detail']['recursive']),

                        ]
                    );
                }

                throw new \Exception('You are not permitted to edit this entity.');
            }

            throw new \Exception('Entity by id not found.');
        } catch (\Exception $ex) {
            return self::json()->failure(
                [
                    'message' => $ex->getMessage(),
                ]
            );
        }
    }

    public function removeAction()
    {
        $id = (int)self::request()->getParam('id');

        try {
            if ($id <= 0) {
                throw new \Exception('Missing required parameter: id');
            }

            /** @var \Favez\ORM\Entity\Repository $repository */
            $repository = $this->getClass()::repository();
            $className  = $this->getClass();
            $model      = $repository->find($id);

            if ($model instanceof $className) {
                if ($this->checkPermission($model, 'remove')) {
                    $model->remove();

                    return self::json()->success();
                }

                throw new \Exception('You are not permitted to remove this entity.');
            }

            throw new \Exception('Entity by id not found.');
        } catch (\Exception $ex) {
            return self::json()->failure(
                [
                    'messae' => $ex->getMessage(),
                ]
            );
        }
    }

    public function batchRemoveAction() {
        $models = self::request()->getParam('data');
        $errors = [];

        /** @var \ProVallo\Plugins\Backend\Components\ModelValidator $validator */
        $validator = self::modelValidator();

        foreach ($models as &$input) {
            $id    = (int)$input['id'];

            /** @var \Favez\ORM\Entity\Repository $repository */
            $repository = $this->getClass()::repository();
            $className  = $this->getClass();

            if ($id > 0) {
                $model = $repository->find($id);

                if (!($model instanceof $className)) {
                    $errors[] = 'Entity by id not found.';
                    continue;
                }

                if (!$this->checkPermission($model, 'remove')) {
                    $errors[] = 'You are not permitted to remove this entity.';
                    continue;
                }
            } else {
                $errors[] = 'Missing required parameter: id';
                continue;
            }

            $model->remove();
        }

        if (empty($errors)) {
            return self::json()->success(
                [
                    'data' => $models,
                ]
            );
        }

        return self::json()->failure(
            [
                'errors' => $errors,
            ]
        );
    }

    public function batchSaveAction()
    {
        $models = self::request()->getParam('data');
        $errors = [];
        $newIds = [];

        /** @var \ProVallo\Plugins\Backend\Components\ModelValidator $validator */
        $validator = self::modelValidator();

        foreach ($models as &$input) {
            $id    = (int)$input['id'];
            $isNew = $id <= 0;

            /** @var \Favez\ORM\Entity\Repository $repository */
            $repository = $this->getClass()::repository();
            $className  = $this->getClass();

            if (!$isNew) {
                $model = $repository->find($id);

                if (!($model instanceof $className)) {
                    $errors[] = 'Entity by id not found.';
                    continue;
                }

                if (!$this->checkPermission($model, 'save')) {
                    $errors[] = 'You are not permitted to edit this entity.';
                    continue;
                }
            } else {
                $model = $repository->create();
                $this->setDefaultValues($model);
            }

            $result = $this->setValues($model, $input);

            if ($result && !isSuccess($result)) {
                $errors[] = $result['message'];
                continue;
            }

            if ($validator->validate($model)) {
                $model->save();
                
                if ($isNew) {
                    $newIds[$id] = $model->id;
                }

                /**
                 * Fixes parentID
                 *
                 * If an entity has children (referencing itself using property parentID)
                 * the associated children probably don't have been created yet so we need
                 * to fix their parentIDs to the new one.
                 */
                if (property_exists($model, 'parentID')) {
                    $parentID = (int) $input['id'];
                    $count = count($models);

                    for ($i = 0; $i < $count; $i++) {
                        if ((int) $models[$i]['parentID'] === $parentID) {
                            $models[$i]['parentID'] = $model->id;
                        }
                    }
                }

                $this->afterSave($model, $isNew);

                $input = $model->toArray($this->config['detail']['recursive']);
                $input = $this->map($input);
                continue;
            }

            $errors[] = $validator->getMessages();
        }

        if (empty($errors)) {
            return self::json()->success(
                [
                    'data'   => $models,
                    'newIds' => $newIds
                ]
            );
        }

        return self::json()->failure(
            [
                'errors' => $errors,
                'newIds' => $newIds
            ]
        );
    }

    /**
     * Maps a row
     *
     * @param array $row
     *
     * @return array
     */
    protected function map($row)
    {
        $row['id'] = (int)$row['id'];

        return $row;
    }

    /**
     * Builds the list query.
     *
     * @return \SelectQuery
     */
    protected function getListQuery()
    {
        $table = $this->getClass()::SOURCE;

        return self::db()->from($table);
    }

    /**
     * Pre-check if the current logged in user is allowed to mutate the entity.
     *
     * @param Entity $entity
     * @param string $action
     *
     * @return bool
     */
    protected function checkPermission(Entity $entity, $action)
    {
        return true;
    }

    /**
     * Applies default values for an entity when it gets created.
     *
     * @param Entity $entity
     */
    protected function setDefaultValues(Entity $entity)
    {
    }

    /**
     * Applies values for an entity from $_POST
     *
     * @param Entity $entity
     * @param array  $input
     */
    protected function setValues(Entity $entity, $input)
    {
    }

    /**
     * Called after the entity were saved.
     *
     * @param Entity  $entity
     * @param boolean $isNew Whether the entity is newly created
     */
    protected function afterSave(Entity $entity, $isNew)
    {
    }

    protected function getClass()
    {
        return $this->config['model'];
    }

    private function mapParents (Entity $entity, array $data, array &$models)
    {
        if (property_exists($model, 'parentID')) {
            $parentID = (int) $input['id'];
            $count = count($models);

            for ($i = 0; $i < $count; $i++) {
                if ((int) $models[$i]['parentID'] === $parentID) {
                    $models[$i]['parentID'] = $model->id;
                }
            }
        }
    }

}