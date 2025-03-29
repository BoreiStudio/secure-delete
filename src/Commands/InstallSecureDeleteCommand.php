<?php

namespace Boreistudio\SecureDelete\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallSecureDeleteCommand extends Command
{
    protected $signature = 'secure-delete:install';
    protected $description = 'Instala el paquete y publica recursos';

    public function handle()
    {
        $this->info('Publicando configuración...');
        $this->call('vendor:publish', [
            '--provider' => 'Boreistudio\SecureDelete\SecureDeleteServiceProvider',
            '--tag' => 'config'
        ]);

        $this->info('Publicando migración...');
        $this->call('vendor:publish', [
            '--provider' => 'Boreistudio\SecureDelete\SecureDeleteServiceProvider',
            '--tag' => 'migrations'
        ]);

        $this->info('Ejecutando migraciones...');
        $this->call('migrate');

        $this->info('¡Paquete instalado!');
    }
}