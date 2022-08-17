<?php
/**
*  @author    mrdotb <hello@mrdotb.com>
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php';

use Bc_Featuredproducts\Install\Installer;
use Bc_Featuredproducts\Product\ProductFeaturedSearchProvider;

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Bc_FeaturedProducts extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'bc_featuredproducts';
        $this->author = 'mrdotb';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Featured products', [], 'Modules.BcFeaturedproducts.Admin');
        $this->description = $this->trans('Choose product to display on the homepage, enhance customer experience with a lively homepage.', [], 'Modules.BcFeaturedproducts.Admin');

        $this->templateFile = 'module:bc_featuredproducts/views/templates/hook/bc_featuredproducts.tpl';
    }

    public function install()
    {
        $installer = new Installer();
        $this->_clearCache('*');

        Configuration::updateValue('BC_HOME_FEATURED_NBR', 8);
        Configuration::updateValue('BC_HOME_FEATURED_CAT', (int) Context::getContext()->shop->getCategory());

        return parent::install()
            && $installer->install($this)
            && $this->registerHook('actionProductAdd')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionProductDelete')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayOrderConfirmation2')
            && $this->registerHook('displayCrossSellingShoppingCart')
            && $this->registerHook('actionCategoryUpdate')
            && $this->registerHook('actionAdminGroupsControllerSaveAfter')
            && $this->registerHook('displayAdminProductsMainStepRightColumnBottom')
        ;
    }

    public function uninstall()
    {
        $installer = new Installer();
        $this->_clearCache('*');

        return parent::uninstall() && $installer->uninstall($this);
    }

    public function hookActionProductAdd($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductDelete($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionAdminGroupsControllerSaveAfter($params)
    {
        $this->_clearCache('*');
    }

    public function hookDisplayAdminProductsMainStepRightColumnBottom($params)
    {
        $product = new Product($params['id_product']);
        $this->context->smarty->assign([
            'featured' => $product->featured
        ]);

        return $this->display(
            __FILE__,
            '/views/templates/hook/bc_featured_field.tpl'
        );
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getContent()
    {
        $output = '';
        $errors = [];

        if (Tools::isSubmit('submitHomeFeatured')) {
            $nbr = Tools::getValue('BC_HOME_FEATURED_NBR');
            if (!Validate::isInt($nbr) || $nbr <= 0) {
                $errors[] = $this->trans('The number of products is invalid. Please enter a positive number.', [], 'Modules.Featuredproducts.Admin');
            }

            $cat = Tools::getValue('HOME_FEATURED_CAT');
            if (!Validate::isInt($cat) || $cat <= 0) {
                $errors[] = $this->trans('The category ID is invalid. Please choose an existing category ID.', [], 'Modules.Featuredproducts.Admin');
            }

            if (count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('BC_HOME_FEATURED_NBR', (int) $nbr);
                Configuration::updateValue('BC_HOME_FEATURED_CAT', (int) $cat);

                $this->_clearCache('*');

                $output = $this->displayConfirmation($this->trans('The settings have been updated.', [], 'Admin.Notifications.Success'));
            }
        }

        return $output . $this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ],

                'description' => $this->trans('To add products to your homepage, go to product catalog and tick the featured input.', [], 'Modules.BcFeaturedproducts.Admin'),
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Number of products to be displayed', [], 'Modules.BcFeaturedproducts.Admin'),
                        'name' => 'BC_HOME_FEATURED_NBR',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Set the number of products that you would like to display on homepage (default: 8).', [], 'Modules.BcFeaturedproducts.Admin'),
                    ],
                    [
                        'type' => 'categories',
                        'tree' => [
                          'id' => 'home_featured_category',
                          'selected_categories' => [Configuration::get('BC_HOME_FEATURED_CAT')],
                        ],
                        'label' => $this->trans('Category from which to pick products to be displayed', [], 'Modules.Featuredproducts.Admin'),
                        'name' => 'HOME_FEATURED_CAT',
                    ]
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitHomeFeatured';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'BC_HOME_FEATURED_NBR' => Tools::getValue('BC_HOME_FEATURED_NBR', (int) Configuration::get('BC_HOME_FEATURED_NBR')),
            'BC_HOME_FEATURED_CAT' => Tools::getValue('BC_HOME_FEATURED_CAT', (int) Configuration::get('BC_HOME_FEATURED_CAT')),
        ];
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('bc_featuredproducts'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('bc_featuredproducts'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $products = $this->getProducts();

        if (!empty($products)) {
            return [
                'products' => $products,
                'allProductsLink' => Context::getContext()->link->getCategoryLink($this->getConfigFieldsValues()['BC_HOME_FEATURED_CAT']),
            ];
        }

        return false;
    }

    protected function getProducts()
    {
        $category = new Category((int) Configuration::get('BC_HOME_FEATURED_CAT'));

        $searchProvider = new ProductFeaturedSearchProvider(
            $this->context->getTranslator()
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = Configuration::get('BC_HOME_FEATURED_NBR');

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        $query->setSortOrder(new SortOrder('product', 'position', 'asc'));

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = $presenterFactory->getPresenter();

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }

    protected function getCacheId($name = null)
    {
        $cacheId = parent::getCacheId($name);
        if (!empty($this->context->customer->id)) {
            $cacheId .= '|' . $this->context->customer->id;
        }

        return $cacheId;
    }
}
