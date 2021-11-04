<?php 
namespace App\Providers;

use App\Http\View\CompanyComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer([
            'master.division._modal',
            'master.position._modal',
            'master.leave-type._modal',
            'master.employee-type._modal',
            'master.employee-level._modal',
            'employee._modal',
            'time-config.index',
            'apikey._modal'
        ], CompanyComposer::class);
    }
}