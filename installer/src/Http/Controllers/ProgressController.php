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

namespace Antares\Installation\Http\Controllers;

use Antares\Foundation\Http\Controllers\BaseController;
use Antares\Installation\Progress;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;

class ProgressController extends BaseController {

    /**
     * @var SolarizedTheme
     */
    protected $theme;

    /**
     * ProgressController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->theme = new SolarizedTheme();
    }

    /**
	 * Setup controller filters.
	 *
	 * @return void
	 */
	protected function setupMiddleware() {

	}

    /**
     * @param Progress $progress
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index(Progress $progress) {
		$previewUrl     = route('installation.installer.progress.preview');
		$consoleTheme   = $this->theme->asArray();

		$progress->start();

		return view('antares/installer::progress', compact('previewUrl', 'consoleTheme'));
	}

    /**
     * @param Progress $progress
     * @return \Illuminate\Http\JsonResponse
     */
	public function preview(Progress $progress) {
        $converter  = new AnsiToHtmlConverter($this->theme);
        $content    = $progress->getOutput();
        $console    = $converter->convert($content);

        if($progress->isFinished()) {
            return response()->json([
                'progress' => $progress->getPercentageProgress(),
                'redirect' => route('installation.installer.completed'),
            ]);
        }

		return response()->json([
		    'console'   => $console,
            'progress'  => $progress->getPercentageProgress(),
        ]);
	}

}
