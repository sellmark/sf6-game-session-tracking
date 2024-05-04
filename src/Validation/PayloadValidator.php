<?php

namespace App\Validation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PayloadValidator
{
    public function __construct(private readonly SerializerInterface $serializer, private readonly ValidatorInterface $validator)
    {
    }

    public function validate(Request $request, string $type): array
    {
        $dto = $this->serializer->deserialize($request->getContent(), $type, 'json');
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return ['errors' => $this->createErrorResponse($errors)];
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
