<?php

declare(strict_types=1);

namespace BagiCraft01\HubDelay;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerQuitEvent;

class Main extends PluginBase implements Listener {

	private array $lastExec = [];

	private Config $config;

	protected function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML);

        if (is_numeric($this->config->get("delay"))) {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
        } else {
            $this->getServer()->getLogger()->error("[HubDelay] Plugin disabled. Please check the config file, there seems to be an error!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		switch ($command->getName()) {
			case 'hub':
				if ($sender instanceof Player) {
					$name = $sender->getName();
					if ((isset($this->lastExec[$name])) && (($this->lastExec[$name] + 5 + $this->config->get("delay")) > (microtime(true)))) {
						$sender->sendMessage($this->config->get("msg_too_fast"));
					} else {
						$this->getScheduler()->scheduleDelayedTask(new HubTask($this, $this->config, $sender->getName()), (20*$this->config->get("delay")));
						$message = str_replace("{delay}", (string)$this->config->get("delay"), $this->config->get("msg_being_teleported"));
						$sender->sendMessage($message);
						$this->lastExec[$name] = microtime(true);
					}
					if (!isset($this->lastExec[$name])) {
						$this->lastExec[$name] = microtime(true);
					}
				} else {
					$sender->sendMessage("§cPlease run this command in-game!");
				}
			break;
		}
		return true;
	}

	public function onPlayerQuit(PlayerQuitEvent $evt) {
		unset($this->lastExec[$evt->getPlayer()->getName()]);
	}
}
