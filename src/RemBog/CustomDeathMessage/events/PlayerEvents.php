<?php

namespace RemBog\CustomDeathMessage\events;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use RemBog\CustomDeathMessage\Main;

class PlayerEvents implements Listener
{
    public function onDeath(PlayerDeathEvent $event): void
    {
        $dead = $event->getPlayer();
        if(!$dead->getLastDamageCause() instanceof EntityDamageByEntityEvent) return;
        $killer = $dead->getLastDamageCause()->getDamager();
        if(!$killer instanceof Player) return;
        
        $nd = $dead->getName();
        $nk = $killer->getName();
        $heal_killer = $killer->getHealth();
        $config = Main::getConfigFile();
        
        $message = str_replace("&", "§", strval($config->get("death-message")));
        $message = str_replace("{killer}", $nk, $message);
        $message = str_replace("{tueur}", $nk, $message);
        $message = str_replace("{line}", "\n", $message);
        $message = str_replace("{mort}", $nd, $message);
        $message = str_replace("{dead}", $nd, $message);
        $message = str_replace("{heal-killer}", $heal_killer, $message);
        $message = str_replace("{soup}", $this->getSoup($killer), $message);
        
        $event->setDeathMessage($message);
        
        //Server::getInstance()->broadcastMessage($message);
    }
    
    public function getSoup(Player $player)
    {
        $soup = 0;
        foreach($player->getInventory()->getContents() as $slot => $item)
        {
            if($item->getId() == ItemIds::MUSHROOM_STEW)
            {
                $soup += $item->getCount();
                if($item->getCount() > 0)
                {
                    return $soup;
                }
                return 0;
            }
        }
    }
}