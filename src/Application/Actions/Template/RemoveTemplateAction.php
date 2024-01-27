<?php

declare(strict_types=1);

namespace App\Application\Actions\Template;

use App\Application\Actions\Template\TemplateAction;
use Psr\Http\Message\ResponseInterface as Response;

class RemoveTemplateAction extends TemplateAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $table = $this->settings->get('db')['templateTable'];
        $formData = $this->getFormData();



        if (empty($formData['template_url_id']) || !is_string($formData['template_url_id'])) {
            return $this->respondWithData(["type" => "error", "message" => "invalid data"], 400);
        }

        $statement = $this->db->prepare("DELETE FROM {$table} WHERE `url_id` = ?  LIMIT 1");

        $result = false;
        $error = null;

        try {
            $result = $statement->execute([$formData['template_url_id']]);
        } catch (\Exception $exception) {
            $result = false;
            $error = $statement->errorInfo()[2];
        }

        return $this->respondWithData(["type" => "response", "result" => $result, "error" => $error]);
    }
}
