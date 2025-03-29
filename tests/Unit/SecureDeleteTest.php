<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SecureDelete\SecureDelete;

class SecureDeleteTest extends TestCase
{
    public function testGutmannMethod()
    {
        $file = public_path('test-gutmann.txt');
        file_put_contents($file, 'test data');
        
        $this->assertTrue(SecureDelete::wipe($file, 'gutmann'));
        $this->assertDatabaseHas('secure_deletions', [
            'deletable_path' => $file,
            'method' => 'gutmann',
        ]);
    }
}
