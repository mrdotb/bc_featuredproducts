<?php
/**
*  @author    mrdotb <hello@mrdotb.com>
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

namespace Bc_Featuredproducts\Product;

use Combination;
use Db;
use DbQuery;
use Product;
use Shop;

class ProductFeatured
{
    /**
     * Get number of featured products.
     *
     * @return int number of featured products
     */
    public static function getNbFeatured()
    {
        $sql = new DbQuery();
        $sql->select('COUNT(*) AS nb');
        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->where('p.featured = 1');
        $sql->where('product_shop.visibility IN ("both", "catalog")');
        $sql->where('product_shop.active = 1');

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getFeatured(
        $idLang,
        $pageNumber,
        $productPerPage
    ) {
        $sql = new DbQuery();
        $sql->select('p.*');
        $sql->select('product_shop.*');
        $sql->select('stock.out_of_stock');
        $sql->select('IFNULL(stock.quantity, 0) AS quantity');
        if (Combination::isFeatureActive()) {
            $sql->select('IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute');
        }
        $sql->select('pl.description, pl.description_short, pl.available_now, pl.available_later, pl.link_rewrite, pl.meta_description, pl.meta_keywords, pl.meta_title, pl.name');
        $sql->select('image_shop.id_image id_image');
        $sql->select('il.legend AS legend');
        $sql->select('m.name AS manufacturer_name');

        $sql->from('product', 'p');

        if (Combination::isFeatureActive()) {
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.id_product = product_attribute_shop.id_product AND product_attribute_shop.default_on = 1');
        }
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product AND pl.id_lang = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . '');
        $sql->join(Product::sqlStock('p', 0));
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.id_product = p.id_product AND image_shop.cover = 1');
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . '');
        $sql->leftJoin('manufacturer', 'm', 'm.id_manufacturer = p.id_manufacturer');

        $sql->where('p.featured = 1');
        $sql->where('product_shop.visibility IN ("both", "catalog")');
        $sql->where('product_shop.active = 1');
        $sql->orderBy('p.date_upd DESC');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
        // Modify SQL result
        return Product::getProductsProperties($idLang, $result);
    }
}
