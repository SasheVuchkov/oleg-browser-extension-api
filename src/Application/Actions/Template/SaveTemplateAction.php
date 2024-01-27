<?php

declare(strict_types=1);

namespace App\Application\Actions\Template;

use App\Application\Settings\SettingsInterface;
use App\Domain\Template\TemplateValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class SaveTemplateAction extends TemplateAction
{
    protected TemplateValidator $validator;

    public function __construct(LoggerInterface $logger, \PDO $db, SettingsInterface $settings, TemplateValidator $validator)
    {
        parent::__construct($logger, $db, $settings);
        $this->validator = $validator;
    }

    protected function generateUrlid() {
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $table = $this->settings->get('db')['templateTable'];
        $formData = $this->request->getParsedBody();

        /*
        if (!$this->validator->isValid($formData)) {
            return $this->respondWithData(["type" => "form_errors", "errors" => $this->validator->errors()], 400);
        }*/

        $urlId = !empty($formData['url_id']) ? $formData['url_id'] : $this->generateUrlid();
        $data = [
            $formData['url'],
            $formData['domain'],
            $formData['name'],
            json_encode($formData['items']),
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
                                                     `items` = ?,
                                                 WHERE `url_id` = ?");
                array_push($data, $urlId);
                $result = $updateStatement->execute($data);
                return $this->respondWithData(["type" => "response", "result" => $result, "count" => $updateStatement->rowCount()]);
            } catch (\Exception $exception) {
                $error = $updateStatement->errorInfo()[2];
                return $this->respondWithData(["type" => "error", "message" => $error], 500);
            }
        }


        $statement = $this->db->prepare("INSERT INTO `{$table}` (`url_id`, `url`, `domain`, `name`, `items`) VALUES (?, ?, ?, ?, ?)");

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
