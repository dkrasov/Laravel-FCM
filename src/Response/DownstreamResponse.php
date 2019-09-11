<?php
declare(strict_types = 1);

namespace LaravelFCM\Response;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DownstreamResponse
 *
 * @package LaravelFCM\Response
 */
class DownstreamResponse extends BaseResponse implements DownstreamResponseContract
{
    public const MULTICAST_ID = 'multicast_id';
    public const CANONICAL_IDS = 'canonical_ids';
    public const RESULTS = 'results';

    public const MISSING_REGISTRATION = 'MissingRegistration';
    public const MESSAGE_ID = 'message_id';
    public const REGISTRATION_ID = 'registration_id';
    public const NOT_REGISTERED = 'NotRegistered';
    public const INVALID_REGISTRATION = 'InvalidRegistration';
    public const UNAVAILABLE = 'Unavailable';
    public const DEVICE_MESSAGE_RATE_EXCEEDED = 'DeviceMessageRateExceeded';
    public const INTERNAL_SERVER_ERROR = 'InternalServerError';

    /**
     * @internal
     *
     * @var int
     */
    protected $numberTokensSuccess = 0;

    /**
     * @internal
     *
     * @var int
     */
    protected $numberTokensFailure = 0;

    /**
     * @internal
     *
     * @var int
     */
    protected $numberTokenModify = 0;

    /**
     * @internal
     *
     * @var
     */
    protected $messageId;

    /**
     * @internal
     *
     * @var array
     */
    protected $tokensToDelete = [];

    /**
     * @internal
     *
     * @var array
     */
    protected $tokensToModify = [];
    /**
     * @internal
     *
     * @var array
     */
    protected $tokensToRetry = [];

    /**
     * @internal
     *
     * @var array
     */
    protected $tokensWithError = [];

    /**
     * @internal
     *
     * @var bool
     */
    protected $hasMissingToken = false;

    /**
     * @internal
     *
     * @var array
     */
    private $tokens;

    /**
     * DownstreamResponse constructor.
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $tokens
     * @throws \LaravelFCM\Response\Exceptions\InvalidRequestException
     * @throws \LaravelFCM\Response\Exceptions\ServerResponseException
     * @throws \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     */
    public function __construct(ResponseInterface $response, $tokens)
    {
        $this->tokens = is_string($tokens) ? [$tokens] : $tokens;

        parent::__construct($response);
    }

    /**
     * Parse the response.
     *
     * @param array $responseInJson
     *
     * @return void
     * @throws \Exception
     */
    protected function parseResponse($responseInJson): void
    {
        $this->parse($responseInJson);

        if ($this->needResultParsing($responseInJson)) {
            $this->parseResult($responseInJson);
        }

        if ($this->logEnabled) {
            $this->logResponse();
        }
    }

    /**
     * @param $responseInJson
     *
     * @return void
     * @internal
     *
     */
    private function parse($responseInJson): void
    {
        if (array_key_exists(self::MULTICAST_ID, $responseInJson)) {
            $this->messageId;
        }

        if (array_key_exists(self::SUCCESS, $responseInJson)) {
            $this->numberTokensSuccess = $responseInJson[self::SUCCESS];
        }

        if (array_key_exists(self::FAILURE, $responseInJson)) {
            $this->numberTokensFailure = $responseInJson[self::FAILURE];
        }

        if (array_key_exists(self::CANONICAL_IDS, $responseInJson)) {
            $this->numberTokenModify = $responseInJson[self::CANONICAL_IDS];
        }
    }

    /**
     * @param $responseInJson
     *
     * @return void
     * @internal
     *
     */
    private function parseResult($responseInJson): void
    {
        foreach ($responseInJson[self::RESULTS] as $index => $result) {
            if (!$this->isSent($result) && !$this->needToBeModify($index, $result) && !$this->needToBeDeleted($index,
                    $result) && !$this->needToResend($index, $result) && !$this->checkMissingToken($result)) {
                $this->needToAddError($index, $result);
            }
        }
    }

    /**
     * @param $responseInJson
     *
     * @return bool
     * @internal
     */
    private function needResultParsing($responseInJson): bool
    {
        return array_key_exists(self::RESULTS,
                $responseInJson) && ($this->numberTokensFailure > 0 || $this->numberTokenModify > 0);
    }

    /**
     * @param $results
     *
     * @return bool
     * @internal
     */
    private function isSent($results): bool
    {
        return array_key_exists(self::MESSAGE_ID, $results) && !array_key_exists(self::REGISTRATION_ID, $results);
    }

    /**
     * @param $index
     * @param $result
     *
     * @return bool
     * @internal
     */
    private function needToBeModify($index, $result): bool
    {
        if (array_key_exists(self::MESSAGE_ID, $result) && array_key_exists(self::REGISTRATION_ID, $result)) {
            if ($this->tokens[$index]) {
                $this->tokensToModify[$this->tokens[$index]] = $result[self::REGISTRATION_ID];
            }

            return true;
        }

        return false;
    }

    /**
     * @param $index
     * @param $result
     *
     * @return bool
     * @internal
     */
    private function needToBeDeleted($index, $result): bool
    {
        if (array_key_exists(self::ERROR, $result) &&
            (in_array(self::NOT_REGISTERED, $result, true) || in_array(self::INVALID_REGISTRATION, $result, true))) {
            if ($this->tokens[$index]) {
                $this->tokensToDelete[] = $this->tokens[$index];
            }

            return true;
        }

        return false;
    }

    /**
     * @param $index
     * @param $result
     *
     * @return bool
     * @internal
     */
    private function needToResend($index, $result): bool
    {
        if (array_key_exists(self::ERROR, $result) && (in_array(self::UNAVAILABLE, $result,
                    true) || in_array(self::DEVICE_MESSAGE_RATE_EXCEEDED, $result,
                    true) || in_array(self::INTERNAL_SERVER_ERROR, $result, true))) {
            if ($this->tokens[$index]) {
                $this->tokensToRetry[] = $this->tokens[$index];
            }

            return true;
        }

        return false;
    }

    /**
     * @param $result
     *
     * @return bool
     * @internal
     */
    private function checkMissingToken($result): bool
    {
        $hasMissingToken = (array_key_exists(self::ERROR, $result) && in_array(self::MISSING_REGISTRATION, $result,
                true));

        $this->hasMissingToken = (bool)($this->hasMissingToken | $hasMissingToken);

        return $hasMissingToken;
    }

    /**
     * @param $index
     * @param $result
     * @return void
     * @internal
     *
     */
    private function needToAddError($index, $result): void
    {
        if (array_key_exists(self::ERROR, $result) && $this->tokens[$index]) {
            $this->tokensWithError[$this->tokens[$index]] = $result[self::ERROR];
        }
    }

    /**
     * @return void
     *@throws \Exception
     * @internal
     *
     */
    protected function logResponse(): void
    {
        $logger = new Logger('Laravel-FCM');
        $logger->pushHandler(new StreamHandler(storage_path('logs/laravel-fcm.log')));

        $logMessage = 'notification send to ' . count($this->tokens) . ' devices' . PHP_EOL;
        $logMessage .= 'success: ' . $this->numberTokensSuccess . PHP_EOL;
        $logMessage .= 'failures: ' . $this->numberTokensFailure . PHP_EOL;
        $logMessage .= 'number of modified token : ' . $this->numberTokenModify . PHP_EOL;

        $logger->info($logMessage);
    }

    /**
     * Merge two response.
     *
     * @param DownstreamResponse $response
     *
     * @return void
     */
    public function merge(DownstreamResponse $response): void
    {
        $this->numberTokensSuccess += $response->numberSuccess();
        $this->numberTokensFailure += $response->numberFailure();
        $this->numberTokenModify += $response->numberModification();

        $this->tokensToDelete = array_merge($this->tokensToDelete, $response->tokensToDelete());
        $this->tokensToModify = array_merge($this->tokensToModify, $response->tokensToModify());
        $this->tokensToRetry = array_merge($this->tokensToRetry, $response->tokensToRetry());
        $this->tokensWithError = array_merge($this->tokensWithError, $response->tokensWithError());
    }

    /**
     * Get the number of device reached with success.
     *
     * @return int
     */
    public function numberSuccess(): int
    {
        return $this->numberTokensSuccess;
    }

    /**
     * Get the number of device which thrown an error.
     *
     * @return int
     */
    public function numberFailure(): int
    {
        return $this->numberTokensFailure;
    }

    /**
     * Get the number of device that you need to modify their token.
     *
     * @return int
     */
    public function numberModification(): int
    {
        return $this->numberTokenModify;
    }

    /**
     * get token to delete.
     *
     * remove all tokens returned by this method in your database
     *
     * @return array
     */
    public function tokensToDelete(): array
    {
        return $this->tokensToDelete;
    }

    /**
     * get token to modify.
     *
     * key: oldToken
     * value: new token
     *
     * find the old token in your database and replace it with the new one
     *
     * @return array
     */
    public function tokensToModify(): array
    {
        return $this->tokensToModify;
    }

    /**
     * Get tokens that you should resend using exponential backoff.
     *
     * @return array
     */
    public function tokensToRetry(): array
    {
        return $this->tokensToRetry;
    }

    /**
     * Get tokens that thrown an error.
     *
     * key : token
     * value : error
     *
     * In production, remove these tokens from you database
     *
     * @return array
     */
    public function tokensWithError(): array
    {
        return $this->tokensWithError;
    }

    /**
     * check if missing tokens was given to the request
     * If true, remove all the empty token in your database.
     *
     * @return bool
     */
    public function hasMissingToken(): bool
    {
        return $this->hasMissingToken;
    }
}
