<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMinimalModule extends Command
{
    protected $signature = 'make:minimal-module {name}';
    protected $description = 'Generate a clean Laravel module with only backend essentials.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $nameLower = Str::lower($name);
        $modulePath = base_path("Modules/{$name}");

        if (File::exists($modulePath)) {
            $this->error("Module '{$name}' already exists.");
            return;
        }

        // Step 1: Create the module using Nwidart
        $this->call('module:make', ['name' => [$name]]);
        $this->info("✅ Module '{$name}' created.");

        // Step 2: Clean out unused frontend + app folders
        File::deleteDirectory("{$modulePath}/app");
        File::deleteDirectory("{$modulePath}/Resources");
        File::deleteDirectory("{$modulePath}/database");
        File::deleteDirectory("{$modulePath}/factories");
        File::deleteDirectory("{$modulePath}/routes");


        File::delete("{$modulePath}/vite.config.js");
        File::delete("{$modulePath}/webpack.mix.js");
        File::delete("{$modulePath}/package.json");
        File::delete("{$modulePath}/composer.json");

        // Step 3: Create backend folder structure
        File::ensureDirectoryExists("{$modulePath}/Providers");
        File::ensureDirectoryExists("{$modulePath}/Http/Controllers");
        File::ensureDirectoryExists("{$modulePath}/Models");
        File::ensureDirectoryExists("{$modulePath}/Routes");
        File::ensureDirectoryExists("{$modulePath}/Database/Seeders");
        File::ensureDirectoryExists("{$modulePath}/Database/Migrations");
        File::ensureDirectoryExists("{$modulePath}/Database/Factories");

        // Step 4: Generate ServiceProvider
        File::put("{$modulePath}/Providers/{$name}ServiceProvider.php", <<<PHP
<?php

namespace Modules\\{$name}\\Providers;

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
PHP);
        $this->info("🧩 {$name}ServiceProvider generated.");

        // Step 5: Generate basic controller
        File::put("{$modulePath}/Http/Controllers/{$name}Controller.php", <<<PHP
<?php

namespace Modules\\{$name}\\Http\\Controllers;

use Illuminate\Routing\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        return response()->json(['message' => '{$name} module controller working!']);
    }
}
PHP);
        $this->info("🧩 {$name}Controller created.");

        // Step 6: Generate model
        File::put("{$modulePath}/Models/{$name}.php", <<<PHP
<?php

namespace Modules\\{$name}\\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$guarded = [];
}
PHP);
        $this->info("🧩 {$name} model created.");

        // Step 7: Generate routes
        File::put("{$modulePath}/Routes/web.php", <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use Modules\\{$name}\\Http\\Controllers\\{$name}Controller;

Route::middleware(['web'])
    ->prefix('{$nameLower}')
    ->group(function () {
        Route::get('/', [{$name}Controller::class, 'index']);
    });
PHP);

        File::put("{$modulePath}/Routes/api.php", <<<PHP
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{$nameLower}')->group(function () {
    // API routes
});
PHP);
        $this->info("🧩 Routes created.");

        // Step 8: Generate Seeder
        File::put("{$modulePath}/Database/Seeders/{$name}DatabaseSeeder.php", <<<PHP
<?php

namespace Modules\\{$name}\\Database\\Seeders;

use Illuminate\Database\Seeder;

class {$name}DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //
    }
}
PHP);
        $this->info("🧩 Seeder created.");

        // Step 9: Update module.json with provider
        $moduleJsonPath = "{$modulePath}/module.json";
        $moduleJson = json_decode(File::get($moduleJsonPath), true);

        $moduleJson['providers'] = [
            "Modules\\{$name}\\Providers\\{$name}ServiceProvider"
        ];

        File::put($moduleJsonPath, json_encode($moduleJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("🔧 module.json updated with provider.");

        // Step 10: Register seeder in root DatabaseSeeder.php
        $this->registerSeeder($name);

        $this->info("✅ Minimal module '{$name}' created successfully.");
    }

    protected function registerSeeder(string $name): void
    {
        $mainSeederPath = database_path('seeders/DatabaseSeeder.php');

        if (!File::exists($mainSeederPath)) {
            $this->warn("⚠️ DatabaseSeeder.php not found — skipping seeder registration.");
            return;
        }

        $content = File::get($mainSeederPath);
        $useStmt = "use Modules\\{$name}\\Database\\Seeders\\{$name}DatabaseSeeder;";
        $callStmt = "\$this->call({$name}DatabaseSeeder::class);";

        if (!Str::contains($content, $useStmt)) {
            $content = preg_replace('/<\?php\s*/', "<?php\n\n{$useStmt}\n", $content);
        }

        if (!Str::contains($content, $callStmt)) {
            $content = preg_replace('/public function run\(\): void\s*\{\s*/', "$0\n        {$callStmt}\n", $content);
        }

        File::put($mainSeederPath, $content);
        $this->info("🧬 Seeder registered in DatabaseSeeder.php.");
    }
}
