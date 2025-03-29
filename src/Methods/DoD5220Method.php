<?php
//USA
namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;
use Boreistudio\SecureDelete\FileShredder;
use Exception;

class DoD5220Method implements SecureDeleteMethodInterface
{
    public static function delete(string $target): bool
    {
        try {
            if (is_string($target) && is_file($target)) {
                return self::deleteFile($target);
            } elseif (is_string($target) && is_dir($target)) {
                return FileShredder::wipeDirectory($target, self::getPatterns());
            } elseif ($target instanceof \Illuminate\Database\Eloquent\Model) {
                return self::deleteModel($target);
            }

            throw new \InvalidArgumentException("Tipo de target no soportado");
        } catch (Exception $e) {
            // Loggear el error si es necesario
            return false;
        }
    }

    public static function deleteFile(string $path): bool
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Archivo no encontrado: $path");
        }
    
        if (!is_writable($path)) {
            throw new \RuntimeException("Sin permisos de escritura: $path");
        }
    
        $success = true;
        foreach (self::getPatterns() as $pattern) {
            if (!FileShredder::overwriteFile($path, $pattern)) {
                $success = false;
                break;
            }
        }
    
        return $success && unlink($path);
    }

    private static function deleteModel($model): bool
    {
        // Sobrescribir datos sensibles antes de borrar
        foreach ($model->getAttributes() as $key => $value) {
            if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                $model->{$key} = str_repeat("\x00", strlen($value));
            }
        }

        return $model->delete();
    }

    public static function getPatterns(): array
    {
        return [
            "\x00",       // Paso 1: Todos ceros
            "\xFF",      // Paso 2: Todos unos
            random_bytes(1) // Paso 3: Byte aleatorio
        ];
    }
}