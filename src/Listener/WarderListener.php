<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Warder\Listener;

use Phoenix\DataMapper\DataMapperResolver;
use Phoenix\Form\FieldDefinitionResolver;
use Phoenix\Record\RecordResolver;
use Windwalker\Core\Application\WebApplication;
use Windwalker\Core\View\BladeHtmlView;
use Windwalker\Event\Event;
use Windwalker\Renderer\BladeRenderer;
use Windwalker\Utilities\Queue\Priority;
use Windwalker\Utilities\Reflection\ReflectionHelper;
use Windwalker\Warder\Helper\WarderHelper;
use Windwalker\Warder\WarderPackage;

/**
 * The SentryListener class.
 *
 * @since  {DEPLOY_VERSION}
 */
class WarderListener
{
	/**
	 * Property package.
	 *
	 * @var  WarderPackage
	 */
	protected $warder;

	/**
	 * UserListener constructor.
	 *
	 * @param WarderPackage $warder
	 */
	public function __construct(WarderPackage $warder = null)
	{
		$this->warder = $warder ? : WarderHelper::getPackage();
	}

	/**
	 * onAfterRouting
	 *
	 * @param Event $event
	 *
	 * @return  void
	 */
	public function onAfterRouting(Event $event)
	{
		/** @var WebApplication $app */
		$app     = $event['app'];
		$package = $app->getPackage();

		// In Warder
		if ($this->warder->isEnabled())
		{
			RecordResolver::addNamespace(ReflectionHelper::getNamespaceName($this->warder) . '/Admin/Record', Priority::LOW);
			DataMapperResolver::addNamespace(ReflectionHelper::getNamespaceName($this->warder) . '/Admin/DataMapper', Priority::LOW);
			FieldDefinitionResolver::addNamespace(ReflectionHelper::getNamespaceName($package) . '/Form');
		}

		// Frontend
		if ($this->warder->isFrontend())
		{
			$package->getMvcResolver()
				->addNamespace(ReflectionHelper::getNamespaceName($this->warder));

			FieldDefinitionResolver::addNamespace((ReflectionHelper::getNamespaceName($this->warder) . '\Form'));
		}
		elseif ($this->warder->isAdmin())
		{
			$package->getMvcResolver()
				->addNamespace(ReflectionHelper::getNamespaceName($this->warder) . '\Admin');

			FieldDefinitionResolver::addNamespace(ReflectionHelper::getNamespaceName($this->warder) . '\Admin\Form');
		}
	}

	/**
	 * onViewBeforeRender
	 *
	 * @param Event $event
	 *
	 * @return  void
	 */
	public function onViewBeforeRender(Event $event)
	{
		$view = $event['view'];

		/**
		 * @var BladeHtmlView $view
		 * @var BladeRenderer $renderer
		 */
		$name = $view->getName();
		$renderer = $view->getRenderer();

		$app = $view->getPackage()->app;

		// Prepare View data
		if ($this->warder->isFrontend())
		{
			// Extends
			$view['warderExtends'] = $this->warder->get('frontend.view.extends', '_global.html');
			$view['warderPrefix'] = $this->warder->get('frontend.language.prefix', 'warder.');
			$view['warder'] = WarderHelper::getPackage();

			// Paths
			$renderer->addPath(WARDER_SOURCE . '/Templates/' . $name . '/' . $app->get('language.locale'), Priority::LOW - 25);
			$renderer->addPath(WARDER_SOURCE . '/Templates/' . $name . '/' . $app->get('language.default'), Priority::LOW - 25);
			$renderer->addPath(WARDER_SOURCE . '/Templates/' . $name, Priority::LOW - 25);
		}
		elseif ($this->warder->isAdmin())
		{
			// Extends
			$view['warderExtends'] = $this->warder->get('admin.view.extends', '_global.html');
			$view['warderPrefix'] = $this->warder->get('admin.language.prefix', 'warder.');
			$view['warder'] = WarderHelper::getPackage();

			// Paths
			$renderer->addPath(WARDER_SOURCE_ADMIN . '/Templates/' . $name . '/' . $app->get('language.locale'), Priority::LOW - 25);
			$renderer->addPath(WARDER_SOURCE_ADMIN . '/Templates/' . $name . '/' . $app->get('language.default'), Priority::LOW - 25);
			$renderer->addPath(WARDER_SOURCE_ADMIN . '/Templates/' . $name, Priority::LOW - 25);
		}
	}
}
