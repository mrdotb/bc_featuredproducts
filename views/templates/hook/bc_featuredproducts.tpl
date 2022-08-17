{**
 *  @author    mrdotb <hello@mrdotb.com>
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}
<section class="featured-products clearfix">
  <h2 class="h2 products-section-title text-uppercase">
    {l s='Our Best Sellers' d='Modules.BcFeaturedProducts.BcFeaturedProducts'}
  </h2>
  {include file="catalog/_partials/productlist.tpl" products=$products cssClass="row" productClass="col-xs-6 col-lg-4 col-xl-3"}
  <a class="all-product-link float-xs-left float-md-right h4" href="{$allProductsLink}">
    {l s='All products' d='Shop.Theme.Catalog'}<i class="material-icons">&#xE315;</i>
  </a>
</section>
