<?php

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        static $user = false;

        if ($user !== false) {
            return $user;
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $user = null;
        }

        $row  = (new \App\Models\UserModel())->find($userId);
        $user = $row ?: null;

        return $user;
    }
}