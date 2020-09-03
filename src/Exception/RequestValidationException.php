<?php

namespace TPG\LightCrudBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends \Exception implements \JsonSerializable
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violations;

    public function __construct(
        ConstraintViolationListInterface $violations,
        string $message = 'Invalid data in request body'
    ) {
        parent::__construct($message);
        $this->violations = $violations;
    }

    /**
     * @param ConstraintViolationListInterface $violations
     * @param string $message
     * @throws RequestValidationException
     */
    public static function assertValid(
        ConstraintViolationListInterface $violations,
        string $message = 'Invalid data in request body'
    ): void {
        if ($violations->count() > 0) {
            throw new self($violations);
        }
    }

    public function violations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function jsonSerialize()
    {
        return [
            'message' => $this->message,
            'errors' => $this->toArray(),
        ];
    }

    public function toArray(): array
    {
        return array_map(static function (ConstraintViolation $violation) {
            return [
                'path' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }, iterator_to_array($this->violations));
    }

}