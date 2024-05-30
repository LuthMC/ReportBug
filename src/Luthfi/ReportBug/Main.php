<?php

namespace Luthfi\ReportBug;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use Luthfi\ReportBug\commands\ReportBugCommand;
use Luthfi\ReportBug\commands\ViewBugsCommand;

class Main extends PluginBase {

    private static $instance;
    private $reportedBugs = [];

    public static function getInstance(): self {
        return self::$instance;
    }

    public function onEnable(): void {
        self::$instance = $this;

        $this->getServer()->getCommandMap()->register("reportbug", new ReportBugCommand($this));
        $this->getServer()->getCommandMap()->register("rb", new ViewBugsCommand($this));

        $this->getLogger()->info(TextFormat::GREEN . "ReportBug plugin enabled.");
    }

    public function addReportedBug(string $playerName, string $bugDescription): void {
        $this->reportedBugs[] = [
            "player" => $playerName,
            "description" => $bugDescription,
            "timestamp" => time()
        ];
    }

    public function getReportedBugs(): array {
        return $this->reportedBugs;
    }
}
