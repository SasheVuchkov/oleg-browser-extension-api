<?php

declare(strict_types=1);

namespace App\Domain\Content;
use Valitron\Validator;

class ContentValidator
{
    protected array $errors = [];

    public function isValid(array $data): bool {
        $validator = new Validator($data);
        $validator->rule('required', ['url', 'domain', 'name'])->message('{field} is required.');
        $validator->rule('array', ['items'])->message('{field} must be an array.');
        $validator->rule('required', ['items.*.id', 'items.*.selector', 'items.*.name'])->message('{field} is required');
        $validator->rule('required', ['scraped.title', 'scraped.content', 'scraped.url'])->message('{field} is required');


        if (!$validator->validate()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    public function errors(): array {
        return $this->errors;
    }
}