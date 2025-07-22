<?php

namespace App\Interfaces;

use App\Models\Admissions\AppProfile;

interface AccountRepositoryInterface
{
    public function registerAccount(array $data): AppProfile;

    public function isPortalClosed(int $programme): bool;

    public function isProgrammeDisabled(int $progtype, int $cos_id, int $cos_id_two): bool;

    public function usernameAlreadyExists(string $username): bool;

    public function loginAccount(array $data): ?array;

    public function resetPassword(array $data): ?array;
}
