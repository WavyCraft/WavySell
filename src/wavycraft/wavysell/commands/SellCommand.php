<?php

declare(strict_types=1);

namespace wavycraft\wavysell\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use wavycraft\wavysell\api\WavySellAPI;

use terpz710\messages\Messages;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;

class SellCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("wavysell.sell");

        $this->registerArgument(0, new IntegerArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $config = new Config(WavySell::getInstance()->getDataFolder() . "messages.yml");

        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return;
        }

        $amount = (int)($args["amount"]);

        if ($amount <= 0) {
            $sender->sendMessage((string) new Messages($config, "invalid-amount"));
            return;
        }

        WavySellAPI::getInstance()->sell($sender, $amount);
    }
}