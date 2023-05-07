<?php
/*
 * File name: ModulesServiceProvider.php
 * Last modified: 2022.04.05 at 16:56:32
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Providers;

use Artisan;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;
use Nwidart\Modules\Facades\Module;

class ModulesServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $this->isInstalledMacro();
            $this->isUpdatedMacro();
            $this->isActivatedMacro();
        } catch (Exception $e) {
            Artisan::call("clear-compiled");
        }

    }

    /**
     * check if the module is installed
     */
    private function isInstalledMacro()
    {
        Module::macro('isInstalled', function ($name) {
            return !ModulesServiceProvider::isContainsUpdate($name);
        });
    }

    /**
     * @param string $id
     * @param $version
     * @return bool
     */
    public static function isContainsUpdate(string $id, $version = null): bool
    {
        $executedMigrations = self::getExecutedMigrations();
        $newMigrations = self::getNewMigrations($id, $version);
        return (!empty($newMigrations)) && count(array_intersect($newMigrations, $executedMigrations->toArray())) < count($newMigrations);
    }

    /**
     * Get the migrations that have already been run.
     *
     * @return Collection List of migrations
     */
    private static function getExecutedMigrations(): Collection
    {
        // migrations table should exist, if not, user will receive an error.
        return DB::table('migrations')->get()->pluck('migration');
    }

    /**
     * @param string $id
     * @param $version
     * @return array|false|string|string[]
     */
    private static function getNewMigrations(string $id, $version = null)
    {
        if (empty($version)) {
            $migrations = glob(module_path($id, 'Database/Migrations') . DIRECTORY_SEPARATOR . '*.php');
        } else {
            $migrations = glob(module_path($id, 'Database/Migrations') . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . '*.php');
        }
        $migrations = array_map(function ($element) {
            return Arr::last(explode(DIRECTORY_SEPARATOR, $element));
        }, $migrations);
        return str_replace('.php', '', $migrations);
    }

    /**
     * check if the module is updated
     */
    private function isUpdatedMacro()
    {
        Module::macro('isUpdated', function ($name, $version) {
            return !ModulesServiceProvider::isContainsUpdate($name, $version);
        });
    }

    /**
     * check if the module is installed and enabled
     */
    private function isActivatedMacro()
    {
        Module::macro('isActivated', function ($name) {
            try {
                return Module::isEnabled($name) && Module::isInstalled($name);
            } catch (ModuleNotFoundException $exception) {
                return false;
            }
        });
    }
}
