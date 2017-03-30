<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Acl;

use Illuminate\Container\Container;

class Migration {
    
    /**
     * Container instance.
     *
     * @var Container 
     */
    protected $container;

    /**
     * Migration constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Returns the Authorization instance for the component.
     *
     * @param string $name
     * @return \Antares\Authorization\Authorization
     */
    protected function getComponentAcl(string $name) {
        return $this->container->make('antares.acl')->make($name);
    }

    /**
     * Returns the Memory Provider instance.
     *
     * @return \Antares\Memory\Provider
     */
    protected function getMemoryProvider() {
        return $this->container->make('antares.memory')->make('component');
    }
    
    /**
     * Set up permissions to the component ACL.
     *
     * @param string $componentFullName
     * @param RoleActionList $roleActionList
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function up(string $componentFullName, RoleActionList $roleActionList) {
        $memory     = $this->getMemoryProvider();
        $acl        = $this->getComponentAcl($componentFullName);
        $roles      = $roleActionList->getRoles();
        $actions    = $roleActionList->getActions();

        $acl->attach($memory);
        $acl->roles()->attach($roles);
        $acl->actions()->attach(self::getFlatActions($actions));

        foreach($roles as $role) {
            $roleActions = self::getFlatActions($roleActionList->getActionsByRole($role));
            $acl->allow($role, $roleActions);
        }

        $acl->save();
        $memory->finish();
    }

    /**
     * Tear down permissions from the component ACL.
     *
     * @param string $componentFullName
     * @throws \RuntimeException
     */
    public function down(string $componentFullName) {
        $memory     = $this->getMemoryProvider();
        $acl        = $this->getComponentAcl($componentFullName);

        $acl->attach($memory);
        $acl->denyAll();
        $acl->save();
        $memory->finish();
    }

    /**
     * Returns a flatten array of actions which only contains friendly action names.
     *
     * @param array $actions
     * @return array
     */
    protected static function getFlatActions(array $actions) {
        $actions = array_map(function(Action $action) {
            return $action->getAction();
        }, $actions);

        return array_unique($actions);
    }
    
}
