<?php
//Canada
namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;

class RcmpMethod implements SecureDeleteMethodInterface
{
    public static function delete(string $target): bool
    {
        if (!file_exists($target)) {
            throw new \RuntimeException("File not found: $target");
        }

        if (!is_writable($target)) {
            throw new \RuntimeException("Write permission denied: $target");
        }

        $patterns = self::getPatterns();
        $fileSize = filesize($target);
        $success = true;

        foreach ($patterns as $pattern) {
            if (!self::overwriteFile($target, $pattern, $fileSize)) {
                $success = false;
                break;
            }
        }

        return $success && unlink($target);
    }

    private static function overwriteFile(string $path, string $pattern, int $fileSize): bool
    {
        $handle = fopen($path, 'wb');
        if ($handle === false) return false;

        $blockSize = 4096; // 4KB blocks
        $blocks = ceil($fileSize / $blockSize);
        $patternBlock = str_repeat($pattern, $blockSize);

        for ($i = 0; $i < $blocks; $i++) {
            if (fwrite($handle, $patternBlock) === false) {
                fclose($handle);
                return false;
            }
        }

        fclose($handle);
        return true;
    }

    public static function getPatterns(): array
    {
        return [
            "\x00", // Step 1: All zeros
            "\xFF", // Step 2: All ones
            "\xAA", // Step 3: 10101010
            "\x55", // Step 4: 01010101
            "\x96", // Step 5: 10010110 (Unique to RCMP)
            "\x69", // Step 6: 01101001 (Complementary to step 5)
            random_bytes(1) // Step 7: Random pattern
        ];
    }
}