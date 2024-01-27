<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Settings\SettingsInterface;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;
    protected SettingsInterface $settings;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, SettingsInterface $settings)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->settings = $settings;
    }
}
