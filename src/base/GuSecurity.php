<?php

namespace gunter1020\yii2\common\base;

use yii\base\Security;

/**
 * Yii2 Security 函示庫 擴充
 *
 * - 支援 設定初始化加解密金鑰
 * - 支援 預設定金鑰加解密
 *
 * ```php
 *  $config = [
 *      'components' => [
 *          'security' => [
 *              'class' => \gunter1020\yii2\common\base\GuSecurity::class,
 *              'encryptKey' => 'YOUR_ENCRYPT_KEY',
 *          ]
 *      ]
 *  ]
 * ```
 *
 * @author Gunter Chou <abcd2221925@gmail.com>
 */
abstract class GuSecurity extends Security
{
    /**
     * 加解密金鑰
     */
    private string $encryptKey;

    /**
     * 設定加解密金鑰
     *
     * @param string $encryptKey 加解密金鑰
     */
    public function setEncryptKey(string $encryptKey): void
    {
        $this->encryptKey = $encryptKey;
    }

    /**
     * 使用預設定金鑰加密
     *
     * @param string $data 明文資料
     * @param string $info (可選) 附加資訊
     *
     * @return string 加密二進制文本
     */
    public function encryptByConfig(string $data, ?string $info = null): string
    {
        return $this->encrypt($data, false, $this->encryptKey, $info);
    }

    /**
     * 使用預設定金鑰解密
     *
     * @param string $data 加密二進制文本
     * @param string $info (可選) 附加資訊
     *
     * @return string|bool 明文資料 或 false 為金鑰驗證錯誤
     */
    public function decryptByConfig(string $data, ?string $info = null): string|bool
    {
        return $this->decrypt($data, false, $this->encryptKey, $info);
    }
}
