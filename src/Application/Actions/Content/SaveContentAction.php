<?php

declare(strict_types=1);

namespace App\Application\Content;

use App\Application\Settings\SettingsInterface;
use App\Domain\Template\TemplateValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Content\ContentAction;

class SaveContentAction extends ContentAction
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
        $formData = $this->getFormData();

        if (!$this->validator->isValid($formData)) {
            return $this->respondWithData(["type" => "form_errors", "errors" => $this->validator->errors()], 400);
        }

        $urlId = !empty($formData['url_id']) ? $formData['url_id'] : $this->generateUrlid();
        $data = [
            $formData['url'],
            $formData['domain'],
            $formData['name'],
            $formData['content'],
            $formData['selector'],
            $formData['metadata'],
        ];

        $result = false;
        $error = null;
        $count = 0;

        if (!empty($formData['url_id'])) {
            try {
                $updateStatement = $this->db->prepare("UPDATE `{$table}` 
                                                 SET `url` = ?,
                                                     `domain` = ?,
                                                     `name` = ?,
                                                     `content` = ?,
                                                     `selector` = ?,
                                                     `metadata` = ?
                                                 WHERE `url_id` = ?");
                array_push($data, $urlId);
                $result = $updateStatement->execute($data);
                return $this->respondWithData(["type" => "response", "result" => $result, "count" => $updateStatement->rowCount()]);
            } catch (\Exception $exception) {
                $error = $updateStatement->errorInfo()[2];
                return $this->respondWithData(["type" => "error", "message" => $error], 500);
            }
        }


        $statement = $this->db->prepare("INSERT INTO `{$table}` (`url_id`, `url`, `domain`, `name`, `content`, `selector`, `metadata`) VALUES (?, ?, ?, ?, ?, ?, ?)");

        try {
            array_unshift($data, $urlId);
            $result = $statement->execute($data);
            return $this->respondWithData(["type" => "response", "result" => $result]);
        } catch (\Exception $exception) {
            $error = $statement->errorInfo()[2];
            return $this->respondWithData(["type" => "error", "message" => $error], 500);
        }
    }
}
