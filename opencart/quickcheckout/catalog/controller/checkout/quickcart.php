<?php
class ControllerCheckoutQuickcart extends Controller {
    public function index() {
        $this->load->language('checkout/cart');

        $response = array();

        if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
            $response['error_warning'] = $this->language->get('error_stock');
        } elseif (isset($this->session->data['error'])) {
            $response['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $response['error_warning'] = '';
        }

        if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
            $response['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
        } else {
            $response['attention'] = '';
        }

        if (isset($this->session->data['success'])) {
            $response['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $response['success'] = '';
        }

		$this->load->model('tool/image');
		$this->load->model('tool/upload');

        $response['products'] = array();

        $products = $this->cart->getProducts();

        foreach ($products as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
			} else {
				$image = '';
			}

            $response['products'][] = array(
                'cart_id'   => $product['cart_id'],
                'thumb'     => $image,
                'name'      => $product['name'],
                'model'     => $product['model'],
                'quantity'  => $product['quantity'],
				'price'     => $product['price'],
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        $response['totals'] = $this->getTotals();
        $response['minimal_total'] = $this->config->get('config_minimal_total');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($response));
    }

    private function getTotals() {
        $this->load->model('setting/extension');
        $totals = array();
        return $totals;
    }
}
