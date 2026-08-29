<?php

namespace App\Common;

// 正規表現チェック
class RegexCheck
{
    //パスワード「大文字半角英字、小文字半角英字、半角数字、混在で8桁以上64桁以下（記号は使えない）」
    public function password(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/\A(?=.*?[a-zA-Z])(?=.*?\d)[a-zA-Z\d]{8,64}\z/",
            $data
        ) === 1;
    }

    // 全角文字（最後にuをつけないとマルチバイトを正しく認識しないらしい）
    public function fullchar(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/^[\p{Hiragana}\p{Katakana}\p{Han}ー々]+$/u",
            $data
        ) === 1;
    }

    // 半角数字
    public function number(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/^(0|[1-9][0-9]*)$/",
            $data
        ) === 1;
    }

    // 電話番号（固定、携帯両方）
    public function phone(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/^(0\d{1,4}-?\d{1,4}-?\d{4})$/",
            $data
        ) === 1;
    }

    // 郵便番号
    public function postnumber(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/^[0-9]{3}-[0-9]{4}$/",
            $data
        ) === 1;
    }

    // 住所
    //全角文字（ひらがな・カタカナ・漢字）
    //全角数字
    //全角英数
    //全角ハイフン
    //全角スペース
    //「・」（中点）
    //「（）」全角括弧
    public function address(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/^[\p{Hiragana}\p{Katakana}\p{Han}０-９Ａ-Ｚａ-ｚ－　・（）]+$/u",
            $data
        ) === 1;
    }

    // 商品説明（最後にuをつけないとマルチバイトを正しく認識しないらしい）
    public function detail(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            "/^[\p{Hiragana}\p{Katakana}\p{Han}ー々　]+$/u",
            $data
        ) === 1;
    }

    // 顧客が退会した時のメールアドレスのダミーであるか調べる正規表現
    // $deletedEmail = 'deleted_' . uniqid('', true)
    public function deletedEmail(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return preg_match(
            '/^deleted_[a-f0-9]+\.[0-9]+$/',
            $data
        ) === 1;
    }

    // 顧客が退会した時のpassword、lastname、firstname、phone、post、addressのダミー[deleted]であるか調べる
    public function deletedCustomer(?string $data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        return $data === '[deleted]';
    }
}
