<?php
/**
 * Basically a really really really nasty hack for updating ping mmlul
 */
namespace Bavfalcon9\ForceUpdatePing;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\network\mcpe\RakLibInterface;
use raklib\protocol\ConnectedPing;
use raklib\server\RakLibServer;

class Loader extends PluginBase {
    /** @var int */
    private $raklibStart;

    public function onEnable(): void {
        $this->saveResource('config.yml');
        $conf = new Config($this->getDataFolder() . 'config.yml');
        $this->raklibStart =  (int) (microtime(true) * 1000);
        $this->getScheduler()->scheduleRepeatingTask(new UpdatePingTask($this), $conf->get('interval') ?? 10);
    }

    public function updatePlayersPing(): void {
        $interfaces = $this->getServer()->getNetwork()->getInterfaces();
        $wantedInterface = null;

        foreach ($interfaces as $interface) {
            if ($interface instanceof RakLibInterface) {
                $wantedInterface = $interface;
                break;
            }
        }

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $time = ((int) (microtime(true) * 1000)) - $this->raklibStart;
            $pk = new ConnectedPing;
            $pk->sendPingTime = $time;

            $ref = new \ReflectionObject($pk);
            $method = $ref->getMethod('encodePayload');
            $method->setAccessible(true);
            $method->invoke($pk);
            $wantedInterface->sendRawPacket($player->getAddress(), $player->getPort(), $pk->getBuffer());
        }
    }
}