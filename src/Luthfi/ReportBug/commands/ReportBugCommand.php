<?php

namespace Luthfi\ReportBug\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Luthfi\ReportBug\Main;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\FormAPI;

class ReportBugCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("reportbug", "Report a bug", "/reportbug");
        $this->setPermission("reportbug.command.reportbug");
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

        $this->showReportForm($sender);
        return true;
    }

    private function showReportForm(Player $player): void {
        $form = new CustomForm(function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }

            $description = $data[0] ?? '';
            if (!empty($description)) {
                $this->plugin->addReportedBug($player->getName(), $description);
                $player->sendMessage("Thank you for reporting the bug!");
            } else {
                $player->sendMessage("You must enter a description to report a bug.");
            }
        });

        $form->setTitle("Report a Bug");
        $form->addInput("Describe the bug:", "Bug description");
        $form->sendToPlayer($player);
    }
}
