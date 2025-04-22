<?php

declare(strict_types=1);

namespace wavycraft\wavysell\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use wavycraft\wavysell\api\WavySellAPI;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class AutoSellCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("wavysell.autosell");

        $this->registerArgument(0, new RawStringArgument("status"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return;
        }

        $statusArg = strtolower($args["status"]);

        $status = $statusArg === "on";

        WavySellAPI::getInstance()->autoSell($sender, $status);
    }
}