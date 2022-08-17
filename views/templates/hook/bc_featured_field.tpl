{**
 *  @author    mrdotb <hello@mrdotb.com>
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}

<div class="form-group mb-4" id="product_featured_div">
  <h2>
    {l s="Featured on homepage" d="Modules.BcFeaturedProducts.BcFeaturedProducts"}
  </h2>
  <div class="row">
    <div class="col-xl-6 col-lg-12">

    <div id="featured_radio">
      <div class="radio form-check form-check-radio">
        <label class="form-check-label">
          <input type="radio" id="featured_0" name="featured" value="1" {if $featured} checked {/if}>
          <i class="form-check-round"></i>
          yes
        </label>
      </div>
      <div class="radio form-check form-check-radio">
        <label class=" form-check-label">
          <input type="radio" id="featured_1" name="featured" value="0" {if !$featured} checked {/if}>
          <i class="form-check-round"></i>
          no
        </label>
      </div> 
    </div>
  </div>
</div>

