<?php

namespace Models;

use Core\Config\DatabaseConfig;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConfig::getConnection();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function fetchAllNoParam(string $sql)
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    protected function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    protected function fromTableGetThis($table, $field, $using, $id)
    {
        $sql = "SELECT {$field} FROM {$table} WHERE {$using} = :value LIMIT 1";
        $result = $this->fetchOne($sql, [
            ':value' => $id
        ]);
        return $result["{$field}"];
    }
    /**
     * Generic decryptor for any sensitive field stored in DB.
     * Expects array keys: {field}_ciphertext, {field}_iv, {field}_tag
     */
    protected function decryptField(array $data, string $field): ?string
    {
        $cipherKey   = $field . '_ciphertext';
        $ivKey       = $field . '_iv';
        $tagKey      = $field . '_tag';

        if (empty($data[$cipherKey] ?? '') || empty($data[$ivKey] ?? '') || empty($data[$tagKey] ?? '')) {
            return '';
        }

        return decrypt_data(
            $data[$cipherKey],
            $data[$ivKey],
            $data[$tagKey]
        );
    }

    protected function decryptEmail(array $user): ?string
    {
        return $this->decryptField($user, 'email');
    }

    protected function decryptPhone(array $user): ?string
    {
        return $this->decryptField($user, 'phone');
    }

    protected function decryptDescription(array $complaint)
    {
        return $this->decryptField($complaint, 'description');
    }
    protected function decryptResponseMessage(array $response)
    {
        return $this->decryptField($response, 'message');
    }
}