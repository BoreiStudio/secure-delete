<?php

namespace Boreistudio\SecureDelete\Methods\interfaces;

interface SecureDeleteMethodInterface
{
    public static function delete(string $target): bool;  // Cambiado a string
    public static function getPatterns(): array;
}