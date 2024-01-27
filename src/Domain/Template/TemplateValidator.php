<?php

declare(strict_types=1);

namespace App\Domain\Template;
use Valitron\Validator;

class TemplateValidator
{
    protected array $errors = [];

    public function isValid(array $data): bool {
        $validator = new Validator($data);
        $validator->rule('required', ['url', "domain", "selector", "name"])->message('{field} is required.');
        if ($validator->validate()) {
            return true;
        }

        $this->errors = $validator->errors();
        return false;
    }

    public function errors(): array {
        return $this->errors;
    }
}