<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDOException;
use RuntimeException;

class Url
{
    private const CODE_ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private const CODE_LENGTH = 6;
    private const MAX_ATTEMPTS = 5;

    public static function createCode(string $longUrl, ?string $expiresAt): string
    {
        $stmt = Database::getConnection()->prepare(
            'INSERT INTO urls (code, long_url, expires_at) VALUES (:code, :long_url, :expires_at)',
        );

        // Retry on the rare random-code collision (UNIQUE constraint).
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = self::generateCode();
            try {
                $stmt->execute([
                    ':code' => $code,
                    ':long_url' => $longUrl,
                    ':expires_at' => $expiresAt,
                ]);

                return $code;
            } catch (PDOException $e) {
                // If the error is not a UNIQUE constraint violation, throw it.
                if ($e->getCode() !== '23000') {
                    throw $e;
                } else {
                    continue;
                }
            }
        }

        throw new RuntimeException('Failed to generate a unique short code.');
    }

    private static function generateCode(): string
    {
        $max = strlen(self::CODE_ALPHABET) - 1;
        $code = '';
        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= self::CODE_ALPHABET[random_int(0, $max)];
        }

        return $code;
    }

    public static function findByCode(string $code): ?array
    {
        $stmt = Database::getConnection()->prepare('SELECT * FROM urls WHERE code = :code');
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public static function findByUrlExpiry(string $longUrl, ?string $expiresAt): ?array
    {
        $stmt = Database::getConnection()->prepare(
            'SELECT * FROM urls WHERE long_url = :long_url AND expires_at IS :expires_at',
        );
        $stmt->execute([
            ':long_url' => $longUrl,
            ':expires_at' => $expiresAt,
        ]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }
}
