<?php namespace App\Services;

use App\Resources\Chef\Services\Menu\MenuService;
use App\Resources\Chef\Services\Menu\MealService;
use App\Resources\Chef\Services\Menu\GroupService;
use App\Resources\Chef\Services\ChefService;
use App\Services\ResponseService;
use DB; 
use Log;

class SearchService {

    protected $menuService;

    protected $mealService;

    protected $groupService;

    protected $chefService;

    protected $responseService;

    function __construct()
    {
        $this->menuService = new MenuService();
        $this->mealService = new MealService();
        $this->groupService = new GroupService();
        $this->chefService = new ChefService();
        $this->responseService = new ResponseService();
        DB::enableQueryLog(); // TODO: Remove
    }

    function __destruct() 
    {
        if(app()->environment() == 'local')
            Log::info(print_r(DB::getQueryLog(), true)); // TODO: Remove
    }

    public function search($request)
    {

        $chefSearchParams = $request->all();
        $menuSearchParams = $request->except(array('lat', 'lng', 'date', 'time'));

        $chefs = $this->chefService->search($chefSearchParams);

        if(count($menuSearchParams))
        {
            foreach($chefs as $key => $chef)
            {
                $menus = $chef->menus()->where(function($query) use($menuSearchParams) {
                    $this->menuService->menuSearch($query, $menuSearchParams);
                })->get();
                
                $chefs[$key]->menus = $menus;
            }
        }

        if($chefs->count() < 1)
            return $this->responseService->notFound('Search results returned nothing');
        else
            return $this->responseService->data($this->chefService->transform($chefs));
    }


} 
