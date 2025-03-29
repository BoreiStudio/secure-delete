<?php

namespace Tests\Feature;

use Boreistudio\SecureDelete\SecureDelete;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SecureDeleteTest extends TestCase
{
    protected string $testFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testFile = public_path('test_delete.txt');
        file_put_contents($this->testFile, 'Test data');
    }

    protected function tearDown(): void
    {
        if (File::exists($this->testFile)) {
            File::delete($this->testFile);
        }
        parent::tearDown();
    }

    /** @dataProvider methodsProvider */
    public function test_secure_delete_methods(string $method)
    {
        $success = SecureDelete::wipe($this->testFile, $method);
        
        $this->assertTrue($success);
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function methodsProvider(): array
    {
        return [
            'DoD 5220.22-M' => ['dod_5220'],
            'Gutmann'       => ['gutmann'],
            'Schneier'      => ['schneier'],
            'Random'        => ['random'],
        ];
    }
}