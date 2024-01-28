<?php

declare(strict_types=1);

namespace App\Application\Actions\Template;

use App\Application\Settings\SettingsInterface;
use App\Domain\Template\TemplateValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class RemoveTemplateAction extends TemplateAction
{
    protected TemplateValidator $validator;

    public function __construct(LoggerInterface $logger, \PDO $db, SettingsInterface $settings, TemplateValidator $validator)
    {
        parent::__construct($logger, $db, $settings);
        $this->validator = $validator;
    }
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $table = $this->settings->get('db')['templateTable'];
        $formData = $this->request->getParsedBody();

        if (empty($formData['url_id'])) {
            return $this->respondWithData(["type" => "form_errors", "message" => "Invalid form data."], 400);
        }
        $data = [
            $formData['url_id'],
        ];
        $statement = $this->db->prepare("DELETE FROM `{$table}` WHERE `url_id` = ? LIMIT 1");

        try {
            $result = $statement->execute($data);
            return $this->respondWithData(["type" => "response", "result" => $result]);
        } catch (\Exception $exception) {
            $error = $statement->errorInfo()[2];
            return $this->respondWithData(["type" => "error", "message" => $error], 500);
        }
    }
}