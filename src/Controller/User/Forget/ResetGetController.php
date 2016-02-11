<?php
/**
 * Part of Front project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Warder\Controller\User\Forget;

use Phoenix\Controller\Display\ItemDisplayController;
use Windwalker\Core\Authentication\User;
use Windwalker\Core\Frontend\Bootstrap;
use Windwalker\Core\Language\Translator;
use Windwalker\Warder\Helper\UserHelper;
use Windwalker\Warder\Model\UserModel;
use Windwalker\Warder\View\User\UserHtmlView;

/**
 * The GetController class.
 * 
 * @since  1.0
 */
class ResetGetController extends ItemDisplayController
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'user';

	/**
	 * Property itemName.
	 *
	 * @var  string
	 */
	protected $itemName = 'user';

	/**
	 * Property listName.
	 *
	 * @var  string
	 */
	protected $listName = 'user';

	/**
	 * Property model.
	 *
	 * @var  UserModel
	 */
	protected $model;

	/**
	 * Property view.
	 *
	 * @var  UserHtmlView
	 */
	protected $view;

	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$this->view['email'] = $this->input->getEmail('email');
		$this->view['token'] = $this->input->get('token');

		// Check email and token
		$user = User::get(array('email' => $this->view['email']));

		if ($user->isNull())
		{
			$this->backToConfirm(Translator::translate($this->langPrefix . 'user.not.found'));

			return;
		}

		if (!UserHelper::verifyPassword($this->view['token'], $user->reset_token))
		{
			$this->backToConfirm('Invalid Token');

			return;
		}
	}

	/**
	 * backToConfirm
	 *
	 * @param string  $message
	 * @param string  $type
	 *
	 * @return  void
	 */
	protected function backToConfirm($message = null, $type = Bootstrap::MSG_WARNING)
	{
		$this->redirect(
			$this->router->http('forget_confirm', array('token' => $this->view['token'], 'email' => $this->view['email'])),
			$message,
			$type
		);
	}
}
