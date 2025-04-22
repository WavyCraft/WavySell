<?php

namespace wavycraft\wavysell\api;

use pocketmine\player\Player;

use pocketmine\item\StringToItemParser;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use wavycraft\wavysell\WavySell;

use wavycraft\wavyeconomy\api\WavyEconomyAPI;

use terpz710\messages\Messages;

final class WavySellAPI {
    use SingletonTrait;

    protected Config $items;
    protected Config $autoSellData;

    public function __construct() {
        $dataFolder = WavySell::getInstance()->getDataFolder();
        $this->items = new Config($dataFolder . "items.yml");
        @mkdir($dataFolder . "database/");
        $this->autoSellData = new Config($dataFolder . "database/auto_sell.json");
    }

    public function sell(Player $player, int $amount) : void{
        $inventory = $player->getInventory();
        $itemsConfig = $this->items->get("items", []);
        $message = new Config(WavySell::getInstance()->getDataFolder() . "messages.yml");
        $sold = 0;
        $totalMoney = 0;

        foreach ($itemsConfig as $itemData) {
            $parsedItem = StringToItemParser::getInstance()->parse($itemData["id"]);
            if ($parsedItem === null) continue;

            $price = $itemData["price"];

            foreach ($inventory->getContents() as $slot => $item) {
                if ($sold >= $amount) break 2;

                if ($item->equals($parsedItem)) {
                    $countToSell = min($item->getCount(), $amount - $sold);
                    $item->setCount($item->getCount() - $countToSell);

                    if ($item->getCount() <= 0) {
                        $inventory->clear($slot);
                    } else {
                        $inventory->setItem($slot, $item);
                    }

                    $sold += $countToSell;
                    $totalMoney += $countToSell * $price;
                }
            }
        }

        if ($totalMoney > 0) {
            WavyEconomyAPI::getInstance()->addMoney($player->getName(), $totalMoney);
            $player->sendMessage((string) new Messages($message, "sold-item", ["{quantity}", "{money}", "{item_name}"], [number_format($sold), number_format($totalMoney), $item->getVanillaName()]));
        } else {
            $player->sendMessage((string) new Messages($message, "not-holding-sellable-item"));
        }
    }

    public function sellAll(Player $player) : void{
        $inventory = $player->getInventory();
        $itemsConfig = $this->items->get("items", []);
        $message = new Config(WavySell::getInstance()->getDataFolder() . "messages.yml");
        $totalMoney = 0;
        $sold = 0;

        foreach ($itemsConfig as $itemData) {
            $parsedItem = StringToItemParser::getInstance()->parse($itemData["id"]);
            if ($parsedItem === null) continue;

            $price = $itemData["price"];

            foreach ($inventory->getContents() as $slot => $item) {
                if ($item->equals($parsedItem)) {
                    $count = $item->getCount();
                    $totalMoney += $count * $price;
                    $sold += $count;
                    $inventory->clear($slot);
                }
            }
        }

        if ($totalMoney > 0) {
            WavyEconomyAPI::getInstance()->addMoney($player->getName(), $totalMoney);
            $player->sendMessage((string) new Messages($message, "sold-all", ["{quantity}", "{money}"], [number_format($sold), number_format($totalMoney)]));
        } else {
            $player->sendMessage((string) new Messages($message, "no-sellable-item"));
        }
    }

    public function autoSellAll(Player $player) : void{
        $inventory = $player->getInventory();
        $itemsConfig = $this->items->get("items", []);
        $totalMoney = 0;
        $sold = 0;

        foreach ($itemsConfig as $itemData) {
            $parsedItem = StringToItemParser::getInstance()->parse($itemData["id"]);
            if ($parsedItem === null) continue;

            $price = $itemData["price"];

            foreach ($inventory->getContents() as $slot => $item) {
                if ($item->equals($parsedItem)) {
                    $count = $item->getCount();
                    $totalMoney += $count * $price;
                    $sold += $count;
                    $inventory->clear($slot);
                }
            }
        }

        if ($totalMoney > 0) {
            WavyEconomyAPI::getInstance()->addMoney($player->getName(), $totalMoney);
            $player->sendMessage((string) new Messages($message, "auto-sold-all", ["{quantity}", "{money}"], [number_format($sold), number_format($totalMoney)]));
        }
    }

    public function autoSell(Player $player, bool $status) : void{
        $this->autoSellData->set($player->getName(), $status);
        $this->autoSellData->save();
        $message = new Config(WavySell::getInstance()->getDataFolder() . "messages.yml");
        $player->sendMessage((string) new Messages($message, "toggle-auto-sell", ["{status}"], [($status ? "enabled" : "disabled")]));
    }

    public function isAutoSelling(Player $player) : bool{
        return $this->autoSellData->get($player->getName());
    }
}
