<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Requests;

use Rakit\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Application\Http\Constants\StatusCode;
use Vozimsan\Core\Rest\Http\Request;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;

abstract class AbstractFormRequest
{
    use JsonResponseTrait;

    /**
     * @var bool
     */
    protected bool $useData = false;

    /**
     * @param Request $request
     * @param Validator $validator
     */
    public function __construct(
        protected Validator $validator,
        protected Request   $request,
    )
    {
    }

    /**
     * @return array|Request
     */
    public function validate(): Response|array
    {
        $validator = $this->validator->validate($this->request->only(
            $this->getValidationKeys()
        ), $this->rules());

        if ($validator->fails()) {
            return $this->errorHandler("Validation error", $validator->errors()->toArray());
        }

        $this->afterValidationHandler();

        return $validator->getValidatedData();
    }

    /**
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * @param string $message
     * @param array $data
     * @return Response
     */
    protected function errorHandler(string $message, array $data = []): Response
    {
        return $this->error($message, StatusCode::BAD_REQUEST, $this->useData ? $data : []);
    }

    /**
     * @return void
     */
    protected function afterValidationHandler(): void
    {
    }

    /**
     * @return array
     */
    private function getValidationKeys(): array
    {
        $keys = array_keys($this->rules());

        foreach ($keys as $k => $key) {
            if (count(explode('.', $key)) > 1) {
                unset($keys[$k]);
            }
        }

        return $keys;
    }
}