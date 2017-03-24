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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls;

/**
 * @author Mariusz Jucha <mariuszjucha@gmail.com>
 * Date: 24.03.17
 * Time: 11:42
 */
class TextType extends AbstractType
{
    
    public $type = 'text';

    protected function render()
    {
        return view('antares/foundation::form.controls.text', ['control' => $this]);
    }

}