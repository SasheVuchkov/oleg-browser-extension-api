<?php

declare(strict_types=1);

namespace App\Application\Actions\Template;

use Psr\Http\Message\ResponseInterface as Response;

class ListTemplatesAllAction extends TemplateAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $table = $this->settings->get('db')['templateTable'];
        $statement = $this->db->prepare("SELECT `url`, `domain`, `name`, `items`, `url_id` FROM {$table}");
        $statement->execute();
        $templates = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (is_array($templates)) {
            foreach ($templates as $key => $template) {
                $templates[$key]["items"] = json_decode($template["items"]);
            }
        }

        return $this->respondWithData(["type" => "response", "templates" => $templates]);
    }
}
