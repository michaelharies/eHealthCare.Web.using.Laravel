<?php
/*
 * File name: ModuleController.php
 * Last modified: 2022.04.04 at 11:33:01
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use App\DataTables\ModuleDataTable;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Log;
use Nwidart\Modules\Facades\Module;
use Nwidart\Modules\Json;
use Prettus\Repository\Exceptions\RepositoryException;
use Symfony\Component\Console\Output\BufferedOutput;

class ModuleController extends Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Display a listing of the Module.
     *
     * @param ModuleDataTable $moduleDatatable
     * @return mixed
     */
    public function index(ModuleDataTable $moduleDatatable)
    {
        return $moduleDatatable->render('modules.index');
    }

    /**
     * Enable/Disable the specified Module.
     *
     * @param string $id
     * @param Request $request
     *
     * @return Application|Redirector|RedirectResponse
     * @throws RepositoryException
     */
    public function enable(string $id, Request $request)
    {
        if (config('installer.demo_app', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('modules.index'));
        }
        $module = Module::find($id);
        if (empty($module)) {
            Flash::error('Module not found');
            return redirect(route('modules.index'));
        }
        if ($module->isEnabled()) {
            $module->disable();
        } else {
            $module->enable();
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.module')]));

        return redirect(route('modules.index'));
    }

    /**
     * Install the specified Module.
     *
     * @param string $id
     * @param Request $request
     *
     * @return Application|Redirector|RedirectResponse
     * @throws RepositoryException
     */
    public function install(string $id, Request $request)
    {
        if (config('installer.demo_app', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('modules.index'));
        }
        $module = Module::find($id);
        if (empty($module)) {
            Flash::error('Module not found');
            return redirect(route('modules.index'));
        }
        if (!config('installer.demo_app')) {
            $this->clearCache();

            if ($this->isContainsUpdate($id)) {
                $outputLog = new BufferedOutput;
                Artisan::call("module:migrate $id --force --seed", [], $outputLog);
                Log::info($outputLog->fetch());
                Flash::success(__('lang.module_installed_successfully', ['operator' => $id]));
                return redirect(route('modules.index'));
            }
        }
        Flash::warning(__('lang.module_already_installed', ['operator' => $id]));
        return redirect(route('modules.index'));
    }

    /**
     * Update the specified Module.
     *
     * @param string $id
     * @param Request $request
     *
     * @return Application|Redirector|RedirectResponse
     * @throws RepositoryException
     */
    public function update(string $id, Request $request)
    {
        if (config('installer.demo_app', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('modules.index'));
        }
        $module = Module::find($id);
        if (empty($module)) {
            Flash::error('Module not found');
            return redirect(route('modules.index'));
        }
        if (!config('installer.demo_app')) {
            $this->clearCache();
            $moduleJson = Json::make(module_path($id, 'module.json'));
            if ($this->isContainsUpdate($id, $moduleJson->version)) {
                $outputLog = new BufferedOutput;
                $class = $moduleJson->version . '\\\\' . $moduleJson->name . 'Update' . $moduleJson->version . 'Seeder';
                Artisan::call("module:migrate $id --force --subpath=$moduleJson->version", [], $outputLog);
                Artisan::call("module:seed $id --force --class=$class", [], $outputLog);
                Log::info($outputLog->fetch());
                Flash::success(__('lang.module_updated_successfully', ['operator' => $id]));
                return redirect(route('modules.index'));
            }
        }
        Flash::warning(__('lang.module_already_updated', ['operator' => $id]));
        return redirect(route('modules.index'));
    }

    /**
     * @param string $id
     * @param $version
     * @return bool
     */
    private function isContainsUpdate(string $id, $version = null): bool
    {
        $executedMigrations = $this->getExecutedMigrations();
        $newMigrations = $this->getNewMigrations($id, $version);
        return (!empty($newMigrations)) && count(array_intersect($newMigrations, $executedMigrations->toArray())) < count($newMigrations);
    }

    /**
     * Get the migrations that have already been ran.
     *
     * @return Collection List of migrations
     */
    private function getExecutedMigrations(): Collection
    {
        // migrations table should exist, if not, user will receive an error.
        return DB::table('migrations')->get()->pluck('migration');
    }

    /**
     * @param string $id
     * @param $version
     * @return array|false|string|string[]
     */
    private function getNewMigrations(string $id, $version = null)
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

    private function clearCache(): void
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('cache:forget', ['key' => 'spatie.permission.cache']);
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    }
}
