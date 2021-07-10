<?php

namespace Core\Support;

use Core\Config\Support\interactsWithValidatorConfig;
use Core\Support\Formating\MsgParser;

class Validator
{

    use interactsWithValidatorConfig;

    private string $currentInputKey;
    private string $currentSubject;
    private string $currentRule;
    private ?string $currentError = null;

    private bool $isJson = false;

    private object $config;

    public function __construct()
    {
        $this->config = $this->getValidatorConfig();
    }

    public function __destruct()
    {
        if ($this->isJson && !is_null($this->currentError)) $this->customCancel(

            new \Core\Http\Response(
                [
                    'status' => 'error',
                    'message' => $this->currentError
                ],
                500
            )

        );

        if (!is_null($this->currentError)) $this->cancel($this->currentError);
    }

    public function validate(\Core\Http\Request $request, array $data): self
    {

        foreach ($data as $requestKey => $rule_name) {

            $rule_set = explode('|', $rule_name);

            $this->currentInputKey = $requestKey;

            foreach ($rule_set as $rule) {
                $this->currentRule = $rule;
                $this->performRuleValidation($request->input($requestKey));
            }
        }

        return $this;
    }

    private function handleVariableRules(): void
    {
        $rule_set = \Core\Support\Formating\Str::regex(implode('|', (array) $this->config->counting_rules));

        if (preg_match($rule_set, $this->currentRule)) {
            $this->performCountingRuleValidation($this->currentSubject);
        }
    }

    private function performRuleValidation(mixed $subject): void
    {
        $this->currentSubject = $subject;

        if (key_exists($this->currentRule, $this->config->rules) && !preg_match($this->config->rules[$this->currentRule], $this->currentSubject)) {
            $this->setError(MsgParser::format($this->config->error_msgs[$this->currentRule], $this->currentInputKey));
        }

        $this->handleVariableRules();
    }

    private function performCountingRuleValidation(): void
    {

        $rule = preg_split('/[0-9\W]/', $this->currentRule)[0];

        $this->setCountingRules();

        if (key_exists($rule, $this->config->counting_rules)) {

            $this->config->counting_rules[$rule] ?: $this->setError(

                MsgParser::format(
                    $this->config->error_msgs[$rule],
                    $this->currentInputKey,
                    $this->getLimits($rule)
                )

            );
        }
    }

    private function setCountingRules(): void
    {
        $this->config->counting_rules['min'] = strlen($this->currentSubject) > $this->getLimits();
        $this->config->counting_rules['max'] = strlen($this->currentSubject) < $this->getLimits();
        $this->config->counting_rules['fix'] = strlen($this->currentSubject) == $this->getLimits();
    }

    private function getLimits(): int
    {
        return (int) @explode(':', $this->currentRule)[1];
    }

    private function cancel($msg): void
    {
        new \Core\Http\ResponseComplements\redirectResponse(
            'back',
            ['error' => $msg],
            500
        );

        exit;
    }

    private function customCancel($content): void
    {
        $content;
        exit;
    }

    private function setError(string $error): void
    {
        if (is_null($this->currentError)) $this->currentError = $error;
    }

    public function asResponse()
    {
        $this->isJson = true;
    }
}
