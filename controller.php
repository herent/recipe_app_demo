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

defined('C5_EXECUTE') or die('Access Denied.');

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
    protected $appVersionRequired = '8.2.0';
    protected $pkgAllowsFullContentSwap = false;
    protected $pkgContentProvidesFileThumbnails = false;

    /**
     * Package class autoloader registrations
     * The package install helper class, included with this boilerplate, 
     * is activated by default.
     *
     * @see https://goo.gl/4wyRtH
     * @var array
     */
    protected $pkgAutoloaderRegistries = [
        //'src/MyVendor/Statistics' => '\MyVendor\ConcreteStatistics'
    ];

    /**
     * The packages handle.
     * Note that this must be unique in the 
     * entire concrete5 package ecosystem.
     * 
     * @var string
     */
    protected $pkgHandle = 'recipe_app_demo';

    /**
     * The packages version.
     * 
     * @var string
     */
    protected $pkgVersion = '0.9.1';

    /**
     * The packages name.
     * 
     * @var string
     */
    protected $pkgName = 'Recipe App Demo';

    /**
     * The packages description.
     * 
     * @var string
     */
    protected $pkgDescription = 'A sample package using concrete5 express objects and react.js to create a recipe application.';

    /**
     * The packages on start hook that is fired as the CMS is booting up.
     * 
     * @return void
     */
    public function on_start()
    {
        // Add custom logic here that needs to be executed during CMS boot, things
        // such as registering services, assets, etc.
        //require $this->getPackagePath().'/vendor/autoload.php';
    }

    /**
     * The packages install routine.
     * 
     * @return \Concrete\Core\Package\Package
     */
    public function install()
    {
        // Add your custom logic here that needs to be executed BEFORE package install.

        $pkg = parent::install();

        // Add your custom logic here that needs to be executed AFTER package install.
        $this->installExpressObjects($pkg);
        return $pkg;
    }

    /**
     * The packages upgrade routine.
     * 
     * @return void
     */
    public function upgrade()
    {
        // Add your custom logic here that needs to be executed BEFORE package install.

        parent::upgrade();

        // Add your custom logic here that needs to be executed AFTER package upgrade.
        $pkg = PackageFacade::getByHandle($this->getPackageHandle());
        $this->installExpressObjects($pkg);
    }

    /**
     * The packages uninstall routine.
     * 
     * @return void
     */
    public function uninstall()
    {
        // Add your custom logic here that needs to be executed BEFORE package uninstall.

        parent::uninstall();

        // Add your custom logic here that needs to be executed AFTER package uninstall.
    }

    /**
     * Create our basic express objects
     *
     * @return void
     */
    private function installExpressObjects($pkg)
    {
        // The actual objects
        $recipeCategory = Express::buildObject('recipe_category', 'recipe_categories', 'Recipe Category', $pkg);
        $recipe         = Express::buildObject('recipe', 'recipes', 'Recipe', $pkg);
        // Attributes for each

        // name
        $recipeCategory->addAttribute('text', 'Name', 'recipe_category_name');
        // image
        $recipeCategory->addAttribute('image_file', "Category Header Image", "recipe_category_image");
        $recipeCategory->save();

        // title
        $recipe->addAttribute('text', 'Title', 'recipe_name');

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
//        $ingredientsSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings();
//        $ingredientsSettings->setAllowMultipleValues((string) true);
//        $ingredientsSettings->setDisplayOrder("display_asc");
//        $ingredientsSettings->setAllowOtherValues((string) true);
//        $recipe->addAttribute('select', 'Ingredients', 'recipe_ingredients', $ingredientsSettings);

        // ingredients
        $ingredientSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings();
        $ingredientSettings->setMode((string) "rich_text");
        $recipe->addAttribute('textarea', 'Ingredients', 'recipe_ingredients', $ingredientSettings);

        // instructions
        $instructionsSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings();
        $instructionsSettings->setMode((string) "rich_text");
        $recipe->addAttribute('textarea', 'Instructions', 'recipe_instructions', $instructionsSettings);
        $recipe->save();

        // Associations
        $builder = $recipeCategory->buildAssociation();
        $builder->addManyToMany($recipe, "recipe_categories");
        $builder->addOneToOne($recipe, "featured_recipe");
        $builder->save();

        // Edit Forms
        $catForm = $recipeCategory->buildForm("Dashboard Recipe Category Form");
        $catFieldset = $catForm->addFieldset("Basics");
        $catFieldset->addAttributeKeyControl("recipe_category_name");
        $catFieldset->addAttributeKeyControl("recipe_category_image");
//        $catFieldset->addTextControl("", "Will be cropped to 376x192. Use a HDPI image for best results.");
        $catFieldset->addAssociationControl("featured_recipe");
        $catForm = $catForm->save();
        $recipeCategory->getEntity()->setDefaultViewForm($catForm);
        $recipeCategory->getEntity()->setDefaultEditForm($catForm);
        $recipeCategory->save();

        $recipeForm     = $recipe->buildForm("Dashboard Recipe Form");
        $recipeFieldset = $recipeForm->addFieldset("Basics");
        $recipeFieldset->addAttributeKeyControl("recipe_name");
        $recipeFieldset->addAttributeKeyControl("recipe_prep_time");
//        $recipeFieldset->addTextControl("", "Include units like minutes or hours, but do not abbreviate");
        $recipeFieldset->addAttributeKeyControl("recipe_difficulty");
        $recipeFieldset->addAttributeKeyControl("recipe_feeds");
//        $recipeFieldset->addTextControl("", "The number of people this dish feeds, either as a single number (6) or a range (6-7)");
        $recipeFieldset->addAttributeKeyControl("recipe_image");
//        $recipeFieldset->addTextControl("", "This image will be cropped to 344x192 in the header, 344x128 in card view. Use a high res picture for desktop and retina devices.");
        $recipeFieldset->addAttributeKeyControl("recipe_ingredients");
//        $recipeFieldset->addTextControl("", "Ingredients are displayed in order, and should include units, IE '2tbs melted butter'");
        $recipeFieldset->addAttributeKeyControl("recipe_instructions");
//        $recipeFieldset->addTextControl("", "Try to avoid lots of formatting or pictures, a simple numbered list is usually best.");
        $recipeFieldset->addAssociationControl("recipe_categories");
        $recipeForm     = $recipeForm->save();
        $recipe->getEntity()->setDefaultViewForm($recipeForm);
        $recipe->getEntity()->setDefaultEditForm($recipeForm);
        $recipe->save();
        
    }
}