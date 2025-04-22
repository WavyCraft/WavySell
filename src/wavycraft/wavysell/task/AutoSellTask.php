<?php

declare(strict_types=1);

namespace wavycraft\wavysell\task;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use wavycraft\wavysell\api\WavySellAPI;

class AutoSellTask extends Task {

    public function onRun() : void{
        $api = WavySellAPI::getInstance();
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if ($api->isAutoSelling($player)) {
                $api->autoSellAll($player);
            }
        }
    }
}