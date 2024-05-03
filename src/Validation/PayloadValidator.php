<?php

namespace App\Validation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PayloadValidator
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function validate(Request $request, string $type): array
    {
        $dto = $this->serializer->deserialize($request->getContent(), $type, 'json');
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return ['dto' => $dto, 'errors' => $errors];
        }

        return ['dto' => $dto, 'errors' => null];
    }

    public function createErrorResponse(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return $errorMessages;
    }
}