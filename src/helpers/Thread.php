<?php

namespace gunter1020\yii2\common\helpers;

use Exception;

/**
 * Thread
 */
class Thread
{
    /**
     * Create multi process
     *
     * @param callable $callback Child thread to be called function
     * @param array<array<mixed>> $arguments Child thread parameters
     * @param int $childMax Max child thread count
     * @param int $delayUs Delay execution in microseconds
     */
    public static function multiProcess(callable $callback, array $arguments, int $childMax = 8, int $delayUs = 1000): void
    {
        // 當下線程數
        $childCur = 0;

        // 當 子線程未完全退出 或 還有運算資源 則繼續執行
        do {
            // 檢查是否還有運算資源
            $haveResource = count($arguments);

            if ($haveResource) {
                // 建立新線程前 暫停 1 ms 防止瞬間大量新增線程
                usleep($delayUs);

                // 當前線程數加一
                $childCur++;

                // 子線程執行參數
                $arg = array_shift($arguments);

                // 建立 子線程
                $pid = pcntl_fork();
            } else {
                // 無運算資源時不建立新線程，
                // 直接至 主線程區 等待 子線程 結束。
                $pid = true;
            }

            if ($pid || !$haveResource) {
                // 主線程區
                // 當 子線程 大於等於 限制上限 或 無運算資源 時，
                // 等待 一個子線程 結束後才可繼續。
                if ($childCur >= $childMax || !$haveResource) {
                    // 子線程狀態
                    $status = null;

                    // 子線程細項
                    $res = null;

                    // 截獲子線程退出
                    pcntl_wait($status, 0, $res);

                    // 當前線程數減一
                    $childCur--;
                }
            } elseif ($pid === 0) {
                // 子線程區
                call_user_func_array($callback, $arg);
                exit;
            } else {
                // 例外錯誤
                throw new Exception('pcntl_fork failure');
            }
        } while ($childCur > 0 || $haveResource);
    }
}
