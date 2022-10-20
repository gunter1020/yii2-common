<?php

namespace gunter1020\yii2\common\log;

use Exception;
use Ramsey\Uuid\Uuid;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;

/**
 * GoogleTarget records log messages to Google Cloud Logging with GKE.
 *
 * @author Gunter Chou <abcd2221925@gmail.com>
 */
class GuGoogleTarget extends Target
{
    /**
     * Event UUID
     */
    private static string $eventId = '';

    public function init(): void
    {
        parent::init();
        self::$eventId = Uuid::uuid6()->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function export()
    {
        $text = implode("\n", array_map([$this, 'getGoogleLogEntry'], $this->messages)) . "\n";

        if (($fp = @fopen('php://stderr', 'a')) === false) {
            throw new InvalidConfigException("Unable to append to log [php://stderr]");
        }

        // 設置排他鎖
        @flock($fp, LOCK_EX);

        // 寫入錯誤資訊
        @fwrite($fp, $text);

        // 釋放鎖定
        @flock($fp, LOCK_UN);

        // 釋放資源
        @fclose($fp);
    }

    /**
     * Returns Google structured logging
     *
     * @see https://cloud.google.com/logging/docs/structured-logging#special-payload-fields
     * @see https://cloud.google.com/logging/docs/structured-logging
     *
     * @param array<mixed> $message logging info
     */
    protected function getGoogleLogEntry(array $message): string
    {
        [$text, $level, $category, $timestamp] = $message;

        $sourceLocation = [];

        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof Exception || $text instanceof Throwable) {
                $sourceLocation = [
                    'file' => $text->getFile(),
                    'line' => $text->getLine(),
                ];

                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        // check if traces info exists
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $text = "{$text}\n    in {$trace['file']}:{$trace['line']}";
            }
        }

        // response component
        $response = Yii::$app->getResponse();

        // request component
        $request = Yii::$app->getRequest();

        /**
         * user component
         *
         * @var \app\components\User $user
         */
        $user = Yii::$app->get('user', false);

        if ($user && $user->getIdentity(false)) {
            $userId = $user->getId();
        } else {
            $userId = '-';
        }

        return json_encode([
            'time' => date(DATE_RFC3339_EXTENDED, $timestamp),
            'severity' => strtoupper(Logger::getLevelName($level)),
            'message' => $text,
            'httpRequest' => [
                'requestMethod' => $request->getMethod(),
                'requestUrl' => $request->getAbsoluteUrl(),
                'requestSize' => StringHelper::byteLength($request->getRawBody()),
                'status' => $response->getStatusCode(),
                'responseSize' => StringHelper::byteLength($response->content),
                'userAgent' => $request->getUserAgent(),
                'remoteIp' => $request->getUserIP(),
                'serverIp' => $_SERVER['SERVER_ADDR'] ?? '',
                'referer' => $_SERVER['HTTP_REFERER'] ?? '',
                'latency' => (YII_BEGIN_TIME - $_SERVER['REQUEST_TIME_FLOAT']) . 's',
                'protocol' => $response->version,
            ],
            'logging.googleapis.com/sourceLocation' => $sourceLocation,
            'logging.googleapis.com/labels' => [
                'eventId' => self::$eventId,
                'userId' => $userId,
            ],
            'logging.googleapis.com/operation' => [
                'category' => $category,
            ],
        ]);
    }
}
