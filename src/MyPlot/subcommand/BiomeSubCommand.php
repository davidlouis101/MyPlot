<?php
declare(strict_types=1);
namespace MyPlot\subcommand;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use pocketmine\level\biome\Biome;
use pocketmine\level\biome\DesertBiome;
use pocketmine\level\biome\ForestBiome;
use pocketmine\level\biome\IcePlainsBiome;
use pocketmine\level\biome\MountainsBiome;
use pocketmine\level\biome\OceanBiome;
use pocketmine\level\biome\PlainBiome;
use pocketmine\level\biome\RiverBiome;
use pocketmine\level\biome\SmallMountainsBiome;
use pocketmine\level\biome\SwampBiome;
use pocketmine\level\biome\TaigaBiome;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class BiomeSubCommand extends SubCommand
{
	/** @var int[] $biomes */
	private $biomes = ["PLAINS" => Biome::PLAINS, "DESERT" => Biome::DESERT, "MOUNTAINS" => Biome::MOUNTAINS, "FOREST" => Biome::FOREST, "TAIGA" => Biome::TAIGA, "SWAMP" => Biome::SWAMP, "NETHER" => Biome::HELL, "HELL" => Biome::HELL, "ICE" => Biome::ICE_PLAINS];

	/**
	 * @param CommandSender $sender
	 *
	 * @return bool
	 */
	public function canUse(CommandSender $sender) : bool {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.biome");
	}

	/**
	 * @param Player $sender
	 * @param string[] $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args) : bool {
		if(empty($args)) {
			$biomes = TextFormat::WHITE . implode(", ", array_keys($this->biomes));
			$sender->sendMessage($this->translateString("biome.possible", [$biomes]));
			return true;
		}
		$player = $sender->getServer()->getPlayer($sender->getName());
		$biome = strtoupper($args[0]);
		$plot = $this->getPlugin()->getPlotByPosition($player);
		if($plot === null) {
			$sender->sendMessage(TextFormat::RED . $this->translateString("notinplot"));
			return true;
		}
		if($plot->owner !== $sender->getName() and !$sender->hasPermission("myplot.admin.biome")) {
			$sender->sendMessage(TextFormat::RED . $this->translateString("notowner"));
			return true;
		}
		if(is_numeric($biome)) {
			$biome = (int) $biome;
			if($biome > 27 or $biome < 0) {
				$sender->sendMessage(TextFormat::RED . $this->translateString("biome.invalid"));
				$biomes = implode(", ", array_keys($this->biomes));
				$sender->sendMessage(TextFormat::RED . $this->translateString("biome.possible", [$biomes]));
				return true;
			}
			$biome = Biome::getBiome($biome);
		}else{
			if(constant(Biome::class."::".$biome) === null) {
				$sender->sendMessage(TextFormat::RED . $this->translateString("biome.invalid"));
				$biomes = implode(", ", array_keys($this->biomes));
				$sender->sendMessage(TextFormat::RED . $this->translateString("biome.possible", [$biomes]));
				return true;
			}
			$biome = Biome::getBiome(constant(Biome::class."::".$biome));
		}
		if($this->getPlugin()->setPlotBiome($plot, $biome)) {
			$sender->sendMessage($this->translateString("biome.success", [$biome->getName()]));
		}else{
			$sender->sendMessage(TextFormat::RED . $this->translateString("error"));
		}
		return true;
	}

	/**
	 * This is where all the arguments, permissions, sub-commands, etc would be registered
	 */
	protected function prepare() : void {
		$this->registerArgument(0, new class("biome", false) extends StringEnumArgument {
			protected const VALUES = [
				"OCEAN" => OceanBiome::class,
				"PLAINS" => PlainBiome::class,
				"DESERT" => DesertBiome::class,
				"MOUNTAINS" => MountainsBiome::class,
				"FOREST" => ForestBiome::class,
				"TAIGA" => TaigaBiome::class,
				"SWAMP" => SwampBiome::class,
				"RIVER" => RiverBiome::class,
				"ICE_PLAINS" => IcePlainsBiome::class,
				"SMALL_MOUNTAINS" => SmallMountainsBiome::class,
				"BIRCH_FOREST" => ForestBiome::class
			];

			/**
			 * @param string $argument
			 * @param CommandSender $sender
			 *
			 * @return mixed
			 */
			public function parse(string $argument, CommandSender $sender) {
				return Biome::getBiome(constant(Biome::class."::".$argument) ?? Biome::OCEAN); // TODO
			}

			public function getTypeName() : string {
				return "biome";
			}
		});
		// TODO: Implement prepare() method.
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		// TODO: Implement onRun() method.
	}
}