<?php

namespace Luthfi\ReportBug\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Luthfi\ReportBug\Main;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\FormAPI;

class ViewBugsCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("rb", "View reported bugs", "/rb");
        $this->setPermission("reportbug.command.viewbugs");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return true;
        }

        $this->showBugsForm($sender);
        return true;
    }

    private function showBugsForm(Player $player): void {
        $reportedBugs = $this->plugin->getReportedBugs();

        $form = new SimpleForm(function (Player $player, ?int $data) use ($reportedBugs) {
            if ($data === null) {
                return;
            }

            $bug = $reportedBugs[$data] ?? null;
            if ($bug === null) {
                return;
            }

            $this->handleBugReportAction($player, $bug);
        });

        $form->setTitle("Reported Bugs");
        if (empty($reportedBugs)) {
            $form->setContent("No bugs have been reported.");
        } else {
            foreach ($reportedBugs as $index => $bug) {
                $date = date("Y-m-d H:i:s", $bug["timestamp"]);
                $form->addButton("[$index] {$bug['player']} ({$date}): {$bug['description']}");
            }
        }

        $form->sendToPlayer($player);
    }

    private function handleBugReportAction(Player $player, array $bug): void {
        $form = new SimpleForm(function (Player $player, ?int $data) use ($bug) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $this->acceptBugReport($player, $bug);
                    break;
                case 1:
                    $this->unacceptBugReport($player, $bug);
                    break;
            }
        });

        $form->setTitle("Bug Report Actions");
        $form->setContent("Reported by: {$bug['player']}\nDescription: {$bug['description']}");
        $form->addButton("Accept");
        $form->addButton("Unaccept");
        $form->sendToPlayer($player);
    }

    private function acceptBugReport(Player $player, array $bug): void {
        $player->sendMessage("Your Bug Report has been accepted. Thank you for your report!");
    }

    private function unacceptBugReport(Player $player, array $bug): void {
        $player->sendMessage("Your Bug Report has been unaccepted.");
    }
}
