<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Validator;

use DB;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
		Paginator::useBootstrap();
		
		Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {
			$query = DB::table($parameters[0]);
			$column = $query->getGrammar()->wrap($parameters[1]);
			
			$id = isset($parameters[2]) ? $parameters[2] : 0;
			
			if (Schema::hasColumn($parameters[0], 'deleted_at')){
				$query->whereNull('deleted_at');
			}
			
			return ! $query->whereRaw("lower({$column}) = lower(?)", [$value])->where('id', '<>', $id)->count();
		});
	
		Validator::extend('uniqueemail', function ($attribute, $value, $parameters, $validator) {
			$query = DB::table($parameters[0]);
			$column = $query->getGrammar()->wrap($parameters[1]);
			
			$id = isset($parameters[2]) ? $parameters[2] : 0;
			$type = isset($parameters[3]) ? $parameters[3] : null;
			
			if (Schema::hasColumn($parameters[0], 'deleted_at')){
				$query->whereNull('deleted_at');
			}
			
			if ($type)
				$query->where('userable_type', $type);
			else
				$query->whereNull('userable_type');
			
			$query->where(function($query) use ($value, $column) {

				foreach($value as $key => $elem) {
					if ($key == 0)
						$query->whereRaw("concat(',',{$column},',') ILIKE ?", ['%,'.$elem.',%']);
					else	
						$query->orWhereRaw("concat(',',{$column},',') ILIKE ?", ['%,'.$elem.',%']);
				}
			});

			return !$query->where('id', '<>', $id)->count();
		});
		
		Validator::replacer('iunique', function ($message, $attribute, $rule, $parameters) {
			return str_replace(':attribute', $attribute, __('The :attribute has already been taken.'));
		});
		
		Validator::replacer('uniqueemail', function ($message, $attribute, $rule, $parameters) {
			return str_replace(':attribute', $attribute, __('An email address has already been used.'));
		});		
		
		Validator::extend('uniquecontactemail', function ($attribute, $value, $parameters, $validator) {
			$query = DB::table($parameters[0]);
			$column = $query->getGrammar()->wrap($parameters[1]);
			
			$id = isset($parameters[2]) ? $parameters[2] : 0;
			$type = isset($parameters[3]) ? $parameters[3] : null;
			
			if (Schema::hasColumn($parameters[0], 'deleted_at')){
				$query->whereNull('deleted_at');
			}
			
			if ($type)
				$query->where('userable_type', $type);
			else
				$query->whereNull('userable_type');
			
			$query->where(function($query) use ($value, $column) {

				foreach($value as $key => $elem) {
					if ($key == 0)
						$query->whereRaw("concat(',',{$column},',') ILIKE ?", ['%,'.$elem.',%']);
					else	
						$query->orWhereRaw("concat(',',{$column},',') ILIKE ?", ['%,'.$elem.',%']);
				}
			});

			return ! $query->where('id', '<>', $id)->count();
		});	

		Builder::macro('whereILike', function (string $attributes, string $searchTerm) {
			$this->where(function (Builder $query) use ($attributes, $searchTerm) {
				$query->when(str_contains($attribute, '.'),
						function (Builder $query) use ($attribute, $searchTerm) {
							[$relationName, $relationAttribute] = explode('.', $attribute);

							$query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
								$query->where(DB::raw("lower({$relationAttribute})"), 'LIKE', strtolower("{$searchTerm}"));
							});
						},
						function (Builder $query) use ($attribute, $searchTerm) {
							$query->orWhere(DB::raw("lower({$attribute})"), 'LIKE', strtolower("{$searchTerm}"));
						}
					);
			});

			return $this;
		});
    }
}
