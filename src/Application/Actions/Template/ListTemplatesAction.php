<?php

declare(strict_types=1);

namespace App\Application\Actions\Template;

use Psr\Http\Message\ResponseInterface as Response;

class ListTemplatesAction extends TemplateAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $table = $this->settings->get('db')['templateTable'];
        $formData = $this->getFormData();

        sleep(5);
        if (empty($formData['domain']) || !is_string($formData['domain'])) {
            return $this->respondWithData(["type" => "error", "message" => "invalid data"], 400);
        }

        $statement = $this->db->prepare("SELECT `url`, `domain`, `name`, `items`, `url_id` FROM {$table} WHERE `domain` = ?");
        $statement->execute([$formData['domain']]);
        $templates = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (is_array($templates)) {
            foreach ($templates as $key => $template) {
                $templates[$key]["items"] = json_decode($template["items"]);
            }
        }

        return $this->respondWithData(["type" => "response", "templates" => $templates]);
    }
}
