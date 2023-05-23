<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\ValidationException;
use JetBrains\PhpStorm\ArrayShape;

class ItemModel implements Model
{
    public function __construct(
        // public fields for test app only
        public ?int       $id,
        public ?string    $name,
        public ?string    $phone,
        public ?string    $key,
        public ?\DateTime $created_at,
        public ?\DateTime $updated_at,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public static function fromArray($data): self
    {
        $item = new ItemModel(
            (int)$data['id'] ?? null,
            $data['name'] ?? null,
            $data['phone'] ?? null,
            $data['key'] ?? null,
            $data['created_at'] ? \DateTime::createFromFormat(DATE_ATOM, $data['created_at']) : null,
            $data['updated_at'] ? \DateTime::createFromFormat(DATE_ATOM, $data['updated_at']) : null,
        );
        if ($item->validate()) {
            return $item;
        }
        throw new ValidationException();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'key' => $this->key,
            'created_at' => $this->created_at->format(DATE_ATOM),
            'updated_at' => $this->updated_at->format(DATE_ATOM),
        ];
    }

    public function validate(): bool
    {
        // for messages can use some validation lib, i.e Respect\Validation
        return
            mb_strlen($this->key ?? "") <= 255
            && mb_strlen($this->phone ?? "") <= 15
            && (!empty($this->key) && (mb_strlen($this->key ?? "")));
    }
}