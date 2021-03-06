<?php
/**
 * Part of Front project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Warder\Controller\User;

use Phoenix\Controller\Display\DisplayController;
use Lyrasoft\Warder\Helper\UserHelper;
use Lyrasoft\Warder\Helper\WarderHelper;

/**
 * The GetController class.
 * 
 * @since  1.0
 */
class LoginGetController extends DisplayController
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'user';

	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		$return = $this->input->getBase64(
			$this->package->get('frontend.login.return_key', 'return')
		);

		if (UserHelper::isLogin())
		{
			if ($return)
			{
				$this->app->redirect(base64_decode($return));
			}
			else
			{
				$this->app->redirect($this->getHomeRedirect());
			}

			return;
		}

		if ($return)
		{
			$this->setUserState($this->getContext('return'), $return);
		}

		parent::prepareExecute();
	}

	/**
	 * getHomeRedirect
	 *
	 * @return  string
	 */
	protected function getHomeRedirect()
	{
		return $this->router->route(WarderHelper::getPackage()->get('frontend.redirect.login', 'home'));
	}
}
