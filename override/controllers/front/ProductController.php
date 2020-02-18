<?php
class ProductController extends ProductControllerCore
{
    protected function assignCategory()
    {
        if ($this->category !== false && Validate::isLoadedObject($this->category) && $this->category->inShop() && $this->category->isAssociatedToShop()) {
            $path = Tools::getPath($this->category->id, '', true);
        } elseif (Category::inShopStatic($this->product->id_category_default, $this->context->shop)) {
            $this->category = new Category((int)$this->product->id_category_default, (int)$this->context->language->id);
            if (Validate::isLoadedObject($this->category) && $this->category->active && $this->category->isAssociatedToShop()) {
                $path = Tools::getPath((int)$this->product->id_category_default, $this->product->name);
            }
        }
        if (!isset($path) || !$path) {
            $path = Tools::getPath((int)$this->context->shop->id_category, $this->product->name);
        }
        $sub_categories = array();
        if (Validate::isLoadedObject($this->category)) {
            $sub_categories = $this->category->getSubCategories($this->context->language->id, true);
            $this->context->smarty->assign(array(
                'path' => $path,
                'category' => $this->category,
                'subCategories' => $sub_categories,
                'id_category_current' => (int)$this->category->id,
                'id_category_parent' => (int)$this->category->id_parent,
                'return_category_name' => Tools::safeOutput($this->category->getFieldByLang('name')),
                'categories' => Category::getHomeCategories($this->context->language->id, true, (int)$this->context->shop->id)
            ));
        }
        $this->context->smarty->assign(array('HOOK_PRODUCT_FOOTER' => Hook::exec('displayFooterProduct', array('product' => $this->product, 'category' => $this->category))));
    }
}