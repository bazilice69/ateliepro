<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Planos padrão do SaaS (sempre).
        $this->call(PlanoSeeder::class);

        // Dados de demonstração multi-tenant (super_admin + 2 lojas + funcionário).
        $this->call(DemoMultiTenantSeeder::class);
    }
}
