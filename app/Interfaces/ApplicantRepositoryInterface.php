<?php

namespace App\Interfaces;


interface ApplicantRepositoryInterface
{

    public function getdashBoardData($request): ?array;

    public function saveDeclaration(): ?array;
}
