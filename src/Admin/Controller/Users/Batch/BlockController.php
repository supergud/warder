<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Warder\Admin\Controller\Users\Batch;

use Phoenix\Controller\Batch\AbstractBatchController;

/**
 * The PublishController class.
 *
 * @since  {DEPLOY_VERSION}
 */
class BlockController extends AbstractBatchController
{
	/**
	 * Property action.
	 *
	 * @var  string
	 */
	protected $action = 'block';

	/**
	 * Property data.
	 *
	 * @var  array
	 */
	protected $data = array(
		'blocked' => 1
	);

	/**
	 * Property langPrefix.
	 *
	 * @var  string
	 */
	protected $langPrefix = 'warder.';
}
