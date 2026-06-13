<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use App\BookStore\Domain\Exception\InvalidBookNameException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class BookName
{
    public const int MAX_LENGTH = 255;

    public function __construct(
        #[ORM\Column(name: 'name', length: self::MAX_LENGTH)]
        public string $value,
    ) {
        if ('' === trim($value)) {
            throw InvalidBookNameException::blank();
        }

        $length = mb_strlen($value);
        if ($length > self::MAX_LENGTH) {
            throw InvalidBookNameException::tooLong($length, self::MAX_LENGTH);
        }
    }
}
