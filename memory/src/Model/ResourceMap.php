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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Memory\Model;

use Antares\Model\Eloquent;

class ResourceMap extends Eloquent
{

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'tbl_resource_map';

    /**
     * The class name to be used in polymorphic relations.
     * @var string
     */
    protected $morphClass = 'ResourceMap';

    /**
     * 
     * @var type 
     */
    public $fillable = [
        'component', 'resource', 'action'
    ];

}
