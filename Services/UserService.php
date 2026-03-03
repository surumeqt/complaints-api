<?php

namespace Services;

use Exception;
use Models\UserModel;

class UserService
{
    private $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function decodedUserProfile(int $id)
    {
        // return the decoded user profile (dont send password ofc)
        $user = $this->userModel->getDecodedProfileById($id);
        if (!$user) return throw new Exception('User Not Found', 404);
        return $user;
    }
    public function updateFieldsProvided(int $id, array $data)
    {
        // use the id passed to update the user profile (not all user fields should be updated, just the payload sent)
        $updatePayload = [];

        foreach ($data as $field => $value) {
            if (in_array($field, ['phone'])) {
                $encrypted = encrypt_sensitive_fields([$field => $value], [$field]);
                $updatePayload["{$field}_ciphertext"] = $encrypted[$field]['cipher'];
                $updatePayload["{$field}_iv"]       = $encrypted[$field]['iv'];
                $updatePayload["{$field}_tag"]      = $encrypted[$field]['tag'];
            } else {
                $updatePayload[$field] = $value;
            }
        }

        $this->userModel->updateFields($id, $updatePayload);
    }
}