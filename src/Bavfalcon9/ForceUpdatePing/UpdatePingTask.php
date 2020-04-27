<?php
namespace Bavfalcon9\ForceUpdatePing;
use pocketmine\scheduler\Task;

class UpdatePingTask extends Task {
    private $plugin;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(int $tick): void {
        $this->plugin->updatePlayersPing();
    }
}