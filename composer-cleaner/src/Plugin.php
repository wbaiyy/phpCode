<?php
namespace Wbaiyy\ComposerCleaner;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;

/**
 * 清理插件
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_UPDATE_CMD => 'clean',
            ScriptEvents::POST_INSTALL_CMD => 'clean',
        ];
    }

    /**
     * 清理
     *
     * @param Event $event
     */
    public function clean(Event $event)
    {
        $cleaner = new Cleaner(
            $event->getIO(),
            new Filesystem(new ProcessExecutor($event->getIO()))
        );
        $extra = $event->getComposer()->getPackage()->getExtra();
        $cleaner->clean(
            $event->getComposer()->getConfig()->get('vendor-dir'),
            empty($extra['Wbaiyy/composer-cleaner']) ? [] : $extra['Wbaiyy/composer-cleaner']
        );
    }
}
