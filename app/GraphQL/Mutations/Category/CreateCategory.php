<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Category;

use JWTAuth;
use Closure;
use GraphQL;
use App\Models\Category;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateCategory extends Mutation
{
    protected $attributes = [
        'name' => 'createCategory',
        'description' => 'A mutation for create Category'
    ];    

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            $this->auth = null;
        }
        
        return (boolean) $this->auth;
    }

    public function type(): Type
    {
        return GraphQL::type('Category');
    }

    public function args(): array
    {
        return [            
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'max:50'
                ]
            ]
        ];
    }

    public function validationErrorMessages(array $args = []): array {
        return [ 
            'name.required' => 'please enter the category name',
            'name.max' => 'Category name must be less than 50 characters'
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $category = new Category();
        $category->fill($args);
        $category->save();
        return $category;
    }
}