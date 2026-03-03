<?php

namespace Models;

class UserModel extends BaseModel
{
    public function createUser(array $data)
    {
        $userId = generateUserId();

        $sql = "
            INSERT INTO users 
            (id, name, email_ciphertext, email_iv, email_tag, password)
            VALUES 
            (:id, :name, :cipher, :iv, :tag, :password)
        ";

        $this->execute($sql, [
            ':id'       => $userId,
            ':name'     => $data['name'],
            ':cipher'   => $data['email']['cipher'],
            ':iv'       => $data['email']['iv'],
            ':tag'      => $data['email']['tag'],
            ':password' => $this->hashPassword($data['password'])
        ]);

        return $this->getUserRoleAndIdById($userId);
    }

    public function getUserByEmail(string $targetEmail): ?array
    {
        $sql = "
            SELECT id, email_ciphertext, email_iv, email_tag, password, role
            FROM users
        ";

        $users = $this->fetchAll($sql);

        foreach ($users as $user) {

            $decryptedEmail = $this->decryptEmail($user);

            if ($decryptedEmail === $targetEmail) {
                return $user;
            }
        }

        return null;
    }
    public function getUserRoleAndIdById(string $id): array
    {
        $sql = "SELECT id, role FROM users WHERE id = :id";

        return $this->fetchOne($sql, [':id' => $id]);
    }
    public function getDecodedProfileById(string $id)
    {
        $sql = "
            SELECT id, name, email_ciphertext, email_iv, email_tag,
                phone_ciphertext, phone_iv, phone_tag,
                role, created_at
            FROM users
            WHERE id = :id
        ";

        $user = $this->fetchOne($sql, [':id' => $id]);

        if (!$user) {
            return null;
        }

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $this->decryptEmail($user),
            'phone' => $this->decryptPhone($user),
            'role' => $user['role']
        ];
    }
    public function updateFields(string $id, array $data)
    {
        if (empty($data)) return;

        $updates = [];
        $params  = [];

        foreach ($data as $field => $value) {
            $updates[] = "$field = :$field";
            $params[":$field"] = $value;
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        $this->execute($sql, $params + [':id' => $id]);
    }
}