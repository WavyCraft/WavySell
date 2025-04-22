<?php

declare(strict_types=1);

namespace wavycraft\wavysell;

use pocketmine\plugin\PluginBase;

use wavycraft\wavysell\task\AutoSellTask;
use wavycraft\wavysell\commands\SellCommand;
use wavycraft\wavysell\commands\SellAllCommand;
use wavycraft\wavysell\commands\AutoSellCommand;

use CortexPE\Commando\PacketHooker;

class WavySell extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveResource("items.yml");
        $this->saveResource("messages.yml");

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->registerAll("WavySell", [
            new SellCommand($this, "sell", "Sell specific amount of items"),
            new SellAllCommand($this, "sellall", "Sell all sellable items"),
            new AutoSellCommand($this, "autosell", "Toggle auto sell")
        ]);

        $this->getScheduler()->scheduleRepeatingTask(new AutoSellTask(), 20);
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}