<?php

namespace BagiCraft01\HubDelay;

use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class HubTask extends Task {

	private Main $main;

	private string $playerName;

	private Config $config;

	public function __construct(Main $main, Config $config, $playerName) {
		$this->main = $main;
        $this->config = $config;
		$this->playerName = $playerName;
	}

    public function onRun(): void
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        if ($player instanceof Player) {
            $player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
            $message = str_replace("{player}", $this->playerName, $this->config->get("msg_tp_success"));
            $player->sendMessage($message);
        }
    }

}
