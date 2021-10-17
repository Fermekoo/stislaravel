<?php 
namespace App\Http\View;

use App\Repositories\CompanyRepo;
use Illuminate\View\View;

class CompanyComposer
{
    protected $companyRepo;
    public function __construct(CompanyRepo $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }

    public function compose(View $view)
    {
        $view->with('companies', $this->companyRepo->getAll());
    }
}