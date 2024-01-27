<?php

declare(strict_types=1);

namespace App\Application\Actions\Template;

use App\Application\Actions\Action;
use App\Application\Settings\SettingsInterface;
use Psr\Log\LoggerInterface;
use Valitron\Validator;

abstract class TemplateAction extends Action
{
    protected \PDO $db;
    protected SettingsInterface $settings;

    public function __construct(LoggerInterface $logger, \PDO $db, SettingsInterface $settings)
    {
        parent::__construct($logger);
        $this->db = $db;
        $this->settings = $settings;
    }
}
