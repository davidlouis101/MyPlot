<?php
declare(strict_types=1);
namespace MyPlot;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\args\StringEnumArgument;
use CortexPE\Commando\BaseCommand;
use MyPlot\subcommand\AddHelperSubCommand;
use MyPlot\subcommand\AutoSubCommand;
use MyPlot\subcommand\BiomeSubCommand;
use MyPlot\subcommand\ClaimSubCommand;
use MyPlot\subcommand\ClearSubCommand;
use MyPlot\subcommand\DenyPlayerSubCommand;
use MyPlot\subcommand\DisposeSubCommand;
use MyPlot\subcommand\GenerateSubCommand;
use MyPlot\subcommand\GiveSubCommand;
use MyPlot\subcommand\HelpSubCommand;
use MyPlot\subcommand\HomesSubCommand;
use MyPlot\subcommand\HomeSubCommand;
use MyPlot\subcommand\InfoSubCommand;
use MyPlot\subcommand\ListSubCommand;
use MyPlot\subcommand\MiddleSubCommand;
use MyPlot\subcommand\NameSubCommand;
use MyPlot\subcommand\PvpSubCommand;
use MyPlot\subcommand\RemoveHelperSubCommand;
use MyPlot\subcommand\ResetSubCommand;
use MyPlot\subcommand\SetOwnerSubCommand;
use MyPlot\subcommand\SubCommand;
use MyPlot\subcommand\UnDenySubCommand;
use MyPlot\subcommand\WarpSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;

class Commands extends BaseCommand implements PluginIdentifiableCommand
{
	/** @var MyPlot $plugin */
	private $plugin;

	/**
	 * Commands constructor.
	 *
	 * @param MyPlot $plugin
	 */
	public function __construct(MyPlot $plugin) {
		$this->plugin = $plugin;
		parent::__construct($plugin->getLanguage()->get("command.name"), $plugin->getLanguage()->get("command.desc"), [$plugin->getLanguage()->get("command.alias")]);
		$this->setPermission("myplot.command");
		$this->setUsage($plugin->getLanguage()->get("command.usage"));
	}

	/**
	 * @return SubCommand[]
	 */
	public function getCommands() : array {
		return $this->getSubCommands();
	}

	/**
	 * @param SubCommand $command
	 */
	public function loadSubCommand(SubCommand $command) : void {
		$this->registerSubCommand($command);
	}

	/**
	 * @param string $name
	 */
	public function unloadSubCommand(string $name) : void {
		$ref = new \ReflectionClass($this);
		$prop = $ref->getProperty("subCommands");
		$prop->setAccessible(true);
		/** @var SubCommand[] $value */
		$value = $prop->getValue($this);
		if(isset($value[$name])) {
			$command = $value[$name];
			$alias = $command->getAlias();
			unset($value[$alias]);
		}
		unset($value[$name]);
		$prop->setValue($this, $value);
	}

	/**
	 * This is where all the arguments, permissions, sub-commands, etc would be registered
	 */
	protected function prepare() : void {
		$plugin = $this->plugin;
		$i = 0;
		$this->registerArgument($i++, new class("SubCommand", false) extends StringEnumArgument{
			protected const VALUES = [
				"help" => "help",
				"claim" => "claim",
				"generate" => "generate"
			];

			public function parse(string $argument, CommandSender $sender) {
				return $this->getValue($argument);
			}

			public function getTypeName() : string {
				return "IntSubCommand";
			}
		});
		$this->registerArgument($i, new class("SubCommand", false) extends StringEnumArgument{
			protected const VALUES = [
				"h" => "help",
				"c" => "claim",
				"gen" => "generate"
			];

			public function parse(string $argument, CommandSender $sender) {
				return $this->getValue($argument);
			}

			public function getTypeName() : string {
				return "BoolSubCommand";
			}
		});
		$this->loadSubCommand(new HelpSubCommand($plugin, "help", $this));
		/*$this->loadSubCommand(new ClaimSubCommand($plugin, "claim"));
		$this->loadSubCommand(new GenerateSubCommand($plugin, "generate"));
		$this->loadSubCommand(new InfoSubCommand($plugin, "info"));
		$this->loadSubCommand(new AddHelperSubCommand($plugin, "addhelper"));
		$this->loadSubCommand(new RemoveHelperSubCommand($plugin, "removehelper"));
		$this->loadSubCommand(new AutoSubCommand($plugin, "auto"));
		$this->loadSubCommand(new ClearSubCommand($plugin, "clear"));
		$this->loadSubCommand(new DisposeSubCommand($plugin, "dispose"));
		$this->loadSubCommand(new ResetSubCommand($plugin, "reset"));
		$this->loadSubCommand(new BiomeSubCommand($plugin, "biome"));
		$this->loadSubCommand(new HomeSubCommand($plugin, "home"));
		$this->loadSubCommand(new HomesSubCommand($plugin, "homes"));
		$this->loadSubCommand(new NameSubCommand($plugin, "name"));
		$this->loadSubCommand(new GiveSubCommand($plugin, "give"));
		$this->loadSubCommand(new WarpSubCommand($plugin, "warp"));
		$this->loadSubCommand(new MiddleSubCommand($plugin, "middle"));
		$this->loadSubCommand(new DenyPlayerSubCommand($plugin, "denyplayer"));
		$this->loadSubCommand(new UnDenySubCommand($plugin, "undenyplayer"));
		$this->loadSubCommand(new SetOwnerSubCommand($plugin, "setowner"));
		$this->loadSubCommand(new ListSubCommand($plugin, "list"));
		$this->loadSubCommand(new PvpSubCommand($plugin, "pvp"));*/
		$plugin->getLogger()->debug("Commands Registered to MyPlot");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $aliasUsed
	 * @param BaseArgument[] $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if($this->getPlugin()->isDisabled()) {
			$sender->sendMessage($this->plugin->getLanguage()->get("plugin.disabled"));
			return;
		}
		if(isset($args["SubCommand"])) {
			var_dump($args);
			return;
		}
		/** @noinspection PhpParamsInspection */
		$temp = new HelpSubCommand($this->plugin, "help", $this);
		if($temp->canUse($sender))
			$temp->execute($sender, []);
		return;
	}

	/**
	 * @return Plugin
	 */
	public function getPlugin() : Plugin {
		return $this->plugin;
	}
}