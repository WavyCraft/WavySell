<?php

declare(strict_types=1);

namespace wavycraft\wavysell\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use wavycraft\wavysell\api\WavySellAPI;

use CortexPE\Commando\BaseCommand;

class SellAllCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("wavysell.sellall");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return;
        }

        WavySellAPI::getInstance()->sellAll($sender);
    }
}