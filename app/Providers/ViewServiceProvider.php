<?php 
namespace App\Providers;

use App\Http\View\CompanyComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer(['master.division._modal','master.position._modal'], CompanyComposer::class);
    }
}