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


namespace Antares\Extension\Console;

class MigrateCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migration for antares/extension package.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $path = 'src/core/memory/resources/database/migrations';

        $this->call('migrate', ['--path' => $path]);
    }

}