<?php
/**
*  @author    mrdotb <hello@mrdotb.com>
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class Product extends ProductCore
{
    /** @var bool Featured product in homepage */
    public $featured = false;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null) {
        self::$definition['fields']['featured'] = [
            'type' => self::TYPE_BOOL,
            'required' => false,
            'validate' => 'isBool'
        ];
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
