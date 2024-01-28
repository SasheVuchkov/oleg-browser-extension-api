<?php

declare(strict_types=1);

namespace App\Application\Actions\Content;

use App\Application\Actions\Template\TemplateAction;
use App\Application\Settings\SettingsInterface;
use App\Domain\Content\ContentValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class SaveContentAction extends TemplateAction
{
    protected ContentValidator $validator;

    public function __construct(LoggerInterface $logger, \PDO $db, SettingsInterface $settings, ContentValidator $validator)
    {
        parent::__construct($logger, $db, $settings);
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $table = $this->settings->get('db')['contentTable'];
        $formData = $this->request->getParsedBody();

        if (!$this->validator->isValid($formData)) {
            return $this->respondWithData(["type" => "form_errors", "message" => "Invalid form data."], 400);
        }

        $data = [
            $formData['url_id'],
            $formData['scraped']['url'],
            $formData['scraped']['title'],
            $formData['scraped']['content'],
            'browser extension',
        ];

        $statement = $this->db->prepare("INSERT INTO `{$table}` (`template_url_id`, `url`, `title`, `information`, `category`) VALUES (?, ?, ?, ?, ?)");

        try {
            $result = $statement->execute($data);
            return $this->respondWithData(["type" => "response", "result" => $result]);
        } catch (\Exception $exception) {
            $error = $statement->errorInfo()[2];
            return $this->respondWithData(["type" => "error", "message" => $error], 500);
        }
    }
}
