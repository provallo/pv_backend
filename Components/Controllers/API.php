<?php

namespace ProVallo\Plugins\Backend\Components\Controllers;

use Favez\ORM\Entity\Entity;

abstract class API extends \ProVallo\Components\Controller
{
    
    const DEFAULT_CONFIG = [
        'model' => '',
        'allowedActions' => [],
        'detail' => [
            'recursive' => false
        ]
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
    abstract public function configure ();

    /**
     * Make sure the user is logged in before he grants access to api controllers.
     */
    final public function preDispatch ()
    {
        $this->config = $this->configure();
        $this->config = array_replace_recursive(self::DEFAULT_CONFIG, $this->config);
        
        if (!$this->isLoggedIn())
        {
            $this->app()->respond(self::json()->failure(['message' => 'Not logged in']));
            die;
        }
    }
    
    /**
     * Whether the user is authenticated.
     *
     * @return bool
     */
    protected function isLoggedIn ()
    {
        if (self::auth()->isLoggedIn())
        {
            return true;
        }
        
        if (in_array(self::dispatcher()->actionName(), $this->config['allowedActions'] ?? []))
        {
            return true;
        }
        
        return false;
    }

    final public function listAction ()
    {
        $rows = $this->getListQuery()->fetchAll();

        return self::json()->success([
            'data' => $rows
        ]);
    }

    public function saveAction ()
    {
        $input = self::request()->getParams();
        $id    = (int) self::request()->getParam('id');
        $isNew = $id <= 0;

        /** @var \Favez\ORM\Entity\Repository $repository */
        $repository = $this->getClass()::repository();
        $className  = $this->getClass();

        if (!$isNew)
        {
            $model = $repository->find($id);

            if (!($model instanceof $className))
            {
                return self::json()->failure(['message' => 'Entity by id not found.']);
            }

            if (!$this->checkPermission($model, 'save'))
            {
                return self::json()->failure(['message' => 'You are not permitted to edit this entity.']);
            }
        }
        else
        {
            $model = $repository->create();
            $this->setDefaultValues($model);
        }

        $this->setValues($model, $input);

        /** @var \ProVallo\Plugins\Backend\Components\ModelValidator $validator */
        $validator = self::modelValidator();

        if ($validator->validate($model))
        {
            $model->save();

            $this->afterSave($model, $isNew);

            return self::json()->success([
                'data' => $model->toArray(false)
            ]);
        }

        return self::json()->failure([
            'messages' => $validator->getMessages()
        ]);
    }

    public function detailAction()
    {
        $id = (int) self::request()->getParam('id');

        /** @var \Favez\ORM\Entity\Repository $repository */
        $repository = $this->getClass()::repository();
        $className  = $this->getClass();

        if ($id > 0)
        {
            $model = $repository->find($id);

            if (!($model instanceof $className))
            {
                return self::json()->failure(['message' => 'Entity by id not found.']);
            }

            if (!$this->checkPermission($model, 'detail'))
            {
                return self::json()->failure(['message' => 'You are not permitted to edit this entity.']);
            }

            return self::json()->success([
                'data' => $model->toArray($this->config['detail']['recursive'])
            ]);
        }
        else
        {
            return self::json()->failure(['message' => 'Missing required param: id']);
        }
    }

    public function removeAction ()
    {
        $id = (int) self::request()->getParam('id');

        /** @var \Favez\ORM\Entity\Repository $repository */
        $repository = $this->getClass()::repository();
        $className  = $this->getClass();

        if ($id > 0)
        {
            $model = $repository->find($id);

            if (!($model instanceof $className))
            {
                return self::json()->failure(['message' => 'Entity by id not found.']);
            }

            if (!$this->checkPermission($model, 'remove'))
            {
                return self::json()->failure(['message' => 'You are not permitted to remove this entity.']);
            }

            $model->remove();

            return self::json()->success();
        }
        else
        {
            return self::json()->failure(['message' => 'Missing required param: id']);
        }
    }

    /**
     * Builds the list query.
     *
     * @return \SelectQuery
     */
    protected function getListQuery ()
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
    protected function checkPermission (Entity $entity, $action)
    {
        return true;
    }

    /**
     * Applies default values for an entity when it gets created.
     *
     * @param Entity $entity
     */
    protected function setDefaultValues (Entity $entity)
    {

    }

    /**
     * Applies values for an entity from $_POST
     *
     * @param Entity $entity
     * @param array  $input
     */
    protected function setValues (Entity $entity, $input)
    {

    }

    /**
     * Called after the entity were saved.
     *
     * @param Entity  $entity
     * @param boolean $isNew  Whether the entity is newly created
     */
    protected function afterSave (Entity $entity, $isNew)
    {

    }

    protected function getClass()
    {
        return $this->config['model'];
    }

}