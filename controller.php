<?php

namespace Concrete\Package\RecipeAppDemo;

use Core;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Illuminate\Filesystem\Filesystem;
use Express;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Routing\RouterInterface;

/**
 * Package Controller Class.
 *
 * A sample package using concrete5 express objects and react.js to create a recipe application.
 *
 * @author   Jeremy Werst <jeremy.werst@gmail.com>
 * @license  See attached license file
 */
class Controller extends Package
{
    /**
     * Minimum version of concrete5 required to use this package.
     * 
     * @var string
     */
    protected $appVersionRequired               = '8.2.0';
    protected $pkgAllowsFullContentSwap         = false;
    protected $pkgContentProvidesFileThumbnails = false;
    protected $pkgAutoloaderMapCoreExtensions   = true;
    protected $pkgAutoloaderRegistries          = [
        'RecipeAppDemo' => '\RecipeAppDemo'
    ];
    protected $pkgHandle                        = 'recipe_app_demo';
    protected $pkgVersion                       = '1.0.0';
    protected $pkgName                          = 'Recipe App Demo';
    protected $pkgDescription                   = 'A sample package using concrete5 express objects and react.js to create a recipe application.';

    public function on_start()
    {
        $route = $this->app->make(RouterInterface::class);
        $route->register('ajax/recipes/categories', '\RecipeAppDemo\Category::listAll');
        $route->register('ajax/recipes/categories/{categoryID}', '\RecipeAppDemo\Category::listAll');
        $route->register('ajax/recipes/categories/{categoryID}/list', '\RecipeAppDemo\Category::getCategoryRecipes');
        $route->register('ajax/recipes/featured', '\RecipeAppDemo\Category::getFeatured');
        $route->register('ajax/recipes/featured/{categoryID}', '\RecipeAppDemo\Category::getFeatured');
        $route->register('ajax/recipes/list', '\RecipeAppDemo\Recipe::listAll');
        $route->register('ajax/recipes/detail/{recipeID}', '\RecipeAppDemo\Recipe::listAll');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installExpressObjects($pkg);
        return $pkg;
    }

    private function installExpressObjects($pkg)
    {
        $recipeCategory = $this->installRecipeCategory();
        $recipe         = $this->installRecipe();
        $this->createAssociations($recipeCategory, $recipe);
        $this->createRecipeCategoryForm($recipeCategory);
        $this->createRecipeForm($recipe);
    }

    private function installRecipeCategory()
    {
        $recipeCategory = Express::buildObject('recipe_category', 'recipe_categories', 'Recipe Category', $pkg);
        // name
        $recipeCategory->addAttribute('text', 'Name', 'recipe_category_name');
        // image
        $recipeCategory->addAttribute('image_file', "Category Header Image", "recipe_category_image");
        $recipeCategory->save();
        return $recipeCategory;
    }

    private function installRecipe()
    {
        $recipe = Express::buildObject('recipe', 'recipes', 'Recipe', $pkg);
        // name
        $recipe->addAttribute('text', 'Name', 'recipe_name');
        // prep time
        $recipe->addAttribute('text', 'Prep Time', 'recipe_prep_time');
        // difficulty
        $difficultySettings    = new \Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings();
        $difficultyOptionsList = new SelectValueOptionList;
        $difficultyOptions     = array("Easy", "Moderate", "Difficult", "Professional");
        $displayOrder          = 0;
        foreach ($difficultyOptions as $optionValue) {
            $opt = new SelectValueOption();
            $opt->setSelectAttributeOptionValue((string) $optionValue);
            $opt->setIsEndUserAdded(false);
            $opt->setOptionList($difficultyOptionsList);
            $opt->setDisplayOrder($displayOrder);
            $difficultyOptionsList->getOptions()->add($opt);
            ++$displayOrder;
        }
        $difficultySettings->setOptionList($difficultyOptionsList);
        $recipe->addAttribute('select', 'Difficulty', 'recipe_difficulty', $difficultySettings);
        // feeds
        $recipe->addAttribute('text', 'Feeds', 'recipe_feeds');
        // image
        $recipe->addAttribute('image_file', "Image", "recipe_image");
        // ingredients
        $ingredientSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings();
        $ingredientSettings->setMode((string) "rich_text");
        $recipe->addAttribute('textarea', 'Ingredients', 'recipe_ingredients', $ingredientSettings);
        // instructions
        $instructionsSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings();
        $instructionsSettings->setMode((string) "rich_text");
        $recipe->addAttribute('textarea', 'Instructions', 'recipe_instructions', $instructionsSettings);
        $recipe->save();
        return $recipe;
    }

    private function createAssociations($recipeCategory, $recipe)
    {
        $builder = $recipeCategory->buildAssociation();
        $builder->addManyToMany($recipe, "category_recipes");
        $builder->addOneToOne($recipe, "featured_recipe");
        $builder->save();
    }

    private function createRecipeCategoryForm($recipeCategory)
    {
        $catForm     = $recipeCategory->buildForm("Dashboard Recipe Category Form");
        $catFieldset = $catForm->addFieldset("Basics");
        $catFieldset->addAttributeKeyControl("recipe_category_name");
        $catFieldset->addAttributeKeyControl("recipe_category_image");
        // note, this is only in the v8 on github currently
        $catFieldset->addAssociationControl("featured_recipe");
        $catForm     = $catForm->save();
        $recipeCategory->getEntity()->setDefaultViewForm($catForm);
        $recipeCategory->getEntity()->setDefaultEditForm($catForm);
        $recipeCategory->save();
    }

    private function createRecipeForm($recipe)
    {
        $recipeForm     = $recipe->buildForm("Dashboard Recipe Form");
        $recipeFieldset = $recipeForm->addFieldset("Basics");
        $recipeFieldset->addAttributeKeyControl("recipe_name");
        $recipeFieldset->addAttributeKeyControl("recipe_prep_time");
        $recipeFieldset->addAttributeKeyControl("recipe_difficulty");
        $recipeFieldset->addAttributeKeyControl("recipe_feeds");
        $recipeFieldset->addAttributeKeyControl("recipe_image");
        $recipeFieldset->addAttributeKeyControl("recipe_ingredients");
        $recipeFieldset->addAttributeKeyControl("recipe_instructions");
        // note, this is only in the v8 on github currently
        $recipeFieldset->addAssociationControl("recipe_categories");
        $recipeForm     = $recipeForm->save();
        $recipe->getEntity()->setDefaultViewForm($recipeForm);
        $recipe->getEntity()->setDefaultEditForm($recipeForm);
        $recipe->save();
    }
}