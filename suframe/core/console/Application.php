<?php
namespace suframe\core\console;

use Inhere\Console\Contract\CommandInterface;
use Inhere\Console\Router;
use Inhere\Console\Util\FormatUtil;
use Inhere\Console\Util\Show;
use Toolkit\Cli\ColorTag;
use \Inhere\Console\Console as InhereConsole;

class Application extends \Inhere\Console\Application{

	/**
	 * Display the application version information
	 */
	public function showVersionInfo(): void
	{
		$os         = \PHP_OS;
		$date       = \date('Y.m.d');
		$logo       = '';
		$name       = $this->getParam('name', 'Console Application');
		$version    = $this->getParam('version', 'Unknown');
		$publishAt  = $this->getParam('publishAt', 'Unknown');
		$updateAt   = $this->getParam('updateAt', 'Unknown');
		$phpVersion = \PHP_VERSION;

		if ($logoTxt = $this->getLogoText()) {
			$logo = ColorTag::wrap($logoTxt, $this->getLogoStyle());
		}

		/** @var \Inhere\Console\IO\Output $out */
		$out = $this->output;
		$out->aList([
			"$logo\n\n<info>{$name}</info>, Version <comment>$version</comment>\n",
			'System Info'      => "PHP version <info>$phpVersion</info>, on <info>$os</info> system",
			'Application Info' => "Update at <info>$updateAt</info>, publish at <info>$publishAt</info>(current $date)",
		], '', [
			'leftChar' => '',
			'sepChar'  => ' :  '
		]);
	}

	/**
	 * Display the application help information
	 * @param string $command
	 * @throws \ReflectionException
	 */
	public function showHelpInfo(string $command = ''): void
	{
		/** @var \Inhere\Console\IO\Input $in */
		$in = $this->input;

		// display help for a special command
		if ($command) {
			$in->setCommand($command);
			$in->setSOpt('h', true);
			$in->clearArgs();
			$this->dispatch($command);
			return;
		}

		$script = $in->getScript();
		$logo       = '';
		if ($logoTxt = $this->getLogoText()) {
			$logo = ColorTag::wrap($logoTxt, $this->getLogoStyle());
		}
		/** @var \Inhere\Console\IO\Output $out */
		$out = $this->output;
		$out->helpPanel([
			'description'=> "\n" . $logo,
			'usage'   => "$script <info>{command}</info> [--opt -v -h ...] [arg0 arg1 arg2=value2 ...]",
			'example' => [
				"$script help {command} (see a command help information)",
			]
		]);
	}

	/**
	 * Display the application group/command list information
	 */
	public function showCommandList(): void
	{
		/** @var \Inhere\Console\IO\Input $input */
		$input = $this->input;
		// has option: --auto-completion
		$autoComp = $input->getBoolOpt('auto-completion');
		// has option: --shell-env
		$shellEnv = (string)$input->getLongOpt('shell-env', '');

		// php bin/app list --only-name
		if ($autoComp && $shellEnv === 'bash') {
			$this->dumpAutoCompletion($shellEnv, []);
			return;
		}

		/** @var \Inhere\Console\IO\Output $output */
		$output = $this->output;
		/** @var Router $router */
		$router = $this->getRouter();
		$script = $this->getScriptName();

		$hasGroup    = $hasCommand = false;
		$groupArr    = $commandArr = [];
		$placeholder = 'No description of the command';

		// all console groups/controllers
		if ($groups = $router->getControllers()) {
			$hasGroup = true;
			\ksort($groups);
		}

		// all independent commands, Independent, Single, Alone
		if ($commands = $router->getCommands()) {
			$hasCommand = true;
			\ksort($commands);
		}

		// add split title on both exists.
		if (!$autoComp && $hasCommand && $hasGroup) {
			$groupArr[]   = \PHP_EOL . '- <bold>Group Commands</bold>';
			$commandArr[] = \PHP_EOL . '- <bold>Alone Commands</bold>';
		}

		foreach ($groups as $name => $info) {
			$options    = $info['options'];
			$controller = $info['handler'];
			/** @var \Inhere\Console\AbstractHandler $controller */
			$desc    = $controller::getDescription() ?: $placeholder;
			$aliases = $options['aliases'];
			$extra   = $aliases ? ColorTag::wrap(
				' [alias: ' . \implode(',', $aliases) . ']',
				'info'
			) : '';

			// collect
			$groupArr[$name] = $desc . $extra;
		}

		if (!$hasGroup && $this->isDebug()) {
			$groupArr[] = '... Not register any group command(controller)';
		}

		foreach ($commands as $name => $info) {
			$desc    = $placeholder;
			$options = $info['options'];
			$command = $info['handler'];

			/** @var \Inhere\Console\AbstractHandler $command */
			if (\is_subclass_of($command, CommandInterface::class)) {
				$desc = $command::getDescription() ?: $placeholder;
			} elseif ($msg = $options['description'] ?? '') {
				$desc = $msg;
			} elseif (\is_string($command)) {
				$desc = 'A handler : ' . $command;
			} elseif (\is_object($command)) {
				$desc = 'A handler by ' . \get_class($command);
			}

			$aliases = $options['aliases'];
			$extra   = $aliases ? ColorTag::wrap(' [alias: ' . \implode(',', $aliases) . ']', 'info') : '';

			$commandArr[$name] = $desc . $extra;
		}

		if (!$hasCommand && $this->isDebug()) {
			$commandArr[] = '... Not register any alone command';
		}

		// built in commands
		$internalCommands = static::$internalCommands;

		if ($autoComp && $shellEnv === 'zsh') {
			$map = \array_merge($internalCommands, $groupArr, $commandArr);
			$this->dumpAutoCompletion('zsh', $map);
			return;
		}

		\ksort($internalCommands);
		InhereConsole::startBuffer();

		if ($appDesc = $this->getParam('description', '')) {
			$appVer = $this->getParam('version', '');
			InhereConsole::writeln(\sprintf('%s%s' . \PHP_EOL, $appDesc, $appVer ? " (Version: <info>$appVer</info>)" : ''));
		}

		// built in options
		$internalOptions = FormatUtil::alignOptions(self::$globalOptions);

		Show::mList([
			'Usage:'              => "$script <info>{command}</info> [--opt -v -h ...] [arg0 arg1 arg2=value2 ...]",
			'Options:'            => $internalOptions,
			'Internal Commands:'  => $internalCommands,
			'Available Commands:' => \array_merge($groupArr, $commandArr),
		], [
			'sepChar' => '  ',
		]);

		unset($groupArr, $commandArr, $internalCommands);
		InhereConsole::write("More command information, please use: <cyan>$script {command} -h</cyan>");
		InhereConsole::flushBuffer();
	}

	public $disableGlobalOptions = true;
	protected function beforeRenderCommandHelp(array &$help): void
	{
		//这个全局显示命令很烦人
		if($this->disableGlobalOptions && isset($help['Global Options:'])){
			unset($help['Global Options:']);
		}
	}

}