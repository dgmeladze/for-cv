<?php

class ControllerCheckoutQuickcheckout extends Controller
{

    public function index()
    {
        /* general fields */


        if (isset($this->session->data['guest']['firstname'])) {
            $data['firstname'] = $this->session->data['guest']['firstname'];
        } elseif (isset($this->session->data['guest']['firstname'])) {
            $data['firstname'] = '';
        }
        if (isset($this->session->data['guest']['telephone'])) {
            $data['telephone'] = $this->session->data['guest']['telephone'];
        } else {
            $data['telephone'] = '';
        }
        if ($this->customer->isLogged()) {
            $this->load->model('account/customer');
            $this->load->model('checkout/quickcheckout');
            $customerInfo = $this->model_account_customer->getCustomer($this->customer->getId());
            $data['firstname'] = $customerInfo['firstname'];
            $data['telephone'] = $customerInfo['telephone'];
            $data['email'] = $customerInfo['email'];
            $addresses = $this->model_checkout_quickcheckout->getAddresses();


            foreach ($addresses as &$address) {
                $address_components = array_filter([
                    $address['postcode'],
                    $address['city'],
                    $address['address_1'],
                    $address['building'],
                    $address['entrance'],
                    $address['appartment']
                ]);
                $address['address'] = implode(', ', $address_components);
            }

            $data['addresses'] = $addresses;
        }



        if (isset($this->session->data['shipping_address']['address_1'])) {
            $data['address_1'] = $this->session->data['shipping_address']['address_1'];
        } else {
            $data['address_1'] = '';
        }
        /* shiping */
        if (isset($this->session->data['shipping_methods'])) {
            $data['shipping_methods'] = $this->session->data['shipping_methods'];
        } else {
            $data['shipping_methods'] = array();
        }

        if (isset($this->session->data['shipping_method']['code'])) {
            $data['code'] = $this->session->data['shipping_method']['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }
        $data['shipping_address'] = array(
            "country_id" => "176",
            "zone" => "2761",
            "zone_id" => "2761",
            "zone_code" => "2761");
        $data['payment_address'] = $data['shipping_address'];
        /* shiping */

        // Shipping Methods
        $method_data = array();

        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('shipping');

        foreach ($results as $result) {
            if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                $this->load->model('extension/shipping/' . $result['code']);

                $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                if ($quote) {
                    $method_data[$result['code']] = array(
                        'title' => $quote['title'],
                        'quote' => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error' => $quote['error']
                    );
                }
            }
        }

        $sort_order = array();


        if (!$this->filterit && (method_exists($this->load, 'library') || get_class($this->load) == 'agooLoader')) {
            $this->load->library('simple/filterit');
        }
        if (!$this->filterit) {
            $this->filterit = new Simple\Filterit($this->registry);
        }
        $method_data = $this->filterit->filterShipping($method_data, $data['shipping_address']);

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);

        $this->session->data['shipping_methods'] = $method_data;

        if (isset($this->session->data['shipping_methods'])) {
            $data['shipping_methods'] = $this->session->data['shipping_methods'];
        } else {
            $data['shipping_methods'] = array();
        }

        if (isset($this->session->data['shipping_method']['code'])) {
            $data['code'] = $this->session->data['shipping_method']['code'];
        } else {
            $data['code'] = '';
        }

        // Payment Methods
        $method_data = array();

        $results = $this->model_setting_extension->getExtensions('payment');

        $recurring = $this->cart->hasRecurringProducts();

        foreach ($results as $result) {
            if ($this->config->get('payment_' . $result['code'] . '_status')) {
                $this->load->model('extension/payment/' . $result['code']);

                $method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

                if ($method) {
                    if ($recurring) {
                        if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
                            $method_data[$result['code']] = $method;
                        }
                    } else {
                        $method_data[$result['code']] = $method;
                    }
                }
            }
        }

        $sort_order = array();


        if (!$this->filterit && (method_exists($this->load, 'library') || get_class($this->load) == 'agooLoader')) {
            $this->load->library('simple/filterit');
        }
        if (!$this->filterit) {
            $this->filterit = new Simple\Filterit($this->registry);
        }
        $method_data = $this->filterit->filterPayment($method_data, $this->session->data['payment_address']);

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);

        $this->session->data['payment_methods'] = $method_data;

        if (isset($this->session->data['payment_methods'])) {
            $data['payment_methods'] = $this->session->data['payment_methods'];
        } else {
            $data['payment_methods'] = array();
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $data['code'] = $this->session->data['payment_method']['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }
         $data['checkProdutcs'] = $this->cart->hasProducts();
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        // $data['cart'] = $this->load->controller('checkout/quickcart');

        $this->response->setOutput($this->load->view('checkout/quickcheckout', $data));
    }

    public function addOrder()
    {

        $data['lang_id'] = $this->config->get('config_language_id');
        $data['config_text_open_form_send_order'] = $this->config->get('config_text_open_form_send_order');
        $data['color_button_open_form_send_order'] = $this->config->get('config_color_button_open_form_send_order');
        $data['icon_open_form_send_order'] = $this->config->get('config_icon_open_form_send_order');
        $data['config_on_off_qo_product_page'] = $this->config->get('config_on_off_qo_product_page');

        $this->load->language('product/product');


        $json = array();


        $this->load->model('catalog/product');
        $this->load->model('checkout/quickcheckout');
        $order_data['currency_id'] = 1;
        $order_data['currency_code'] = $this->session->data['currency'];
        $order_data['currency_value'] = 1;
        $order_data['ip'] = $this->request->server['REMOTE_ADDR'];
        $order_data['store_id'] = $this->config->get('config_store_id');
        $order_data['store_name'] = $this->config->get('config_name');
        /* customer info */
        $order_info = $this->request->post;
        $this->load->model('account/address');
        $this->load->model('account/customer');
        $this->customer->getId();
        $order_data['customer_id'] = $this->customer->getId();
        $customer_info = $this->model_account_customer->getCustomer($order_data['customer_id']);
        $order_data['firstname'] = $order_info['firstname'];
        $order_data['telephone'] = $order_info['telephone'];
        $order_data['email'] = $order_info['email'];
        $order_data['customer_group_id'] = $customer_info['customer_group_id'];
        $order_data['city'] = $order_info['city'];
        $order_data['address_1'] = $order_info['street'];
        $order_data['building'] = $order_info['building'];
        $order_data['entrance'] = $order_info['entrance'];
        $order_data['appartment'] = $order_info['appartment'];
        // доставка и оплата

        $this->session->data['payment_method'] = $this->session->data['payment_methods'][$order_info['payment_method']];
        if (!isset($this->request->post['shipping_method'])) {
            $json['error']['warning'] = $this->language->get('error_shipping');
        } else {
            $shipping = explode('.', $this->request->post['shipping_method']);

            if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                $json['error']['warning'] = $this->language->get('error_shipping');
            }
        }

        $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
        if (isset($this->session->data['payment_method']['title'])) {
            $order_data['payment_method'] = $this->session->data['payment_method']['title'];
        } else {
            $order_data['payment_method'] = '';
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $order_data['payment_code'] = $this->session->data['payment_method']['code'];
        } else {
            $order_data['payment_code'] = '';
        }
        if (isset($this->session->data['shipping_method']['title'])) {
            $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
        } else {
            $order_data['shipping_method'] = '';
        }

        if (isset($this->session->data['shipping_method']['code'])) {
            $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
        } else {
            $order_data['shipping_code'] = '';
        }

        /* customer info end */
        $order_data['order_status_id'] = $this->config->get('config_order_status_id');

        $order_data['products'] = $this->cart->getProducts();
        $total = 0;
        foreach ($order_data['products'] as $product) {
            $product_total = 0;
            foreach ($order_data['products'] as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }

            }
            $total += $product['total'];
        }
        $order_data['total'] = $total;

        if ($order_data['store_id']) {
            $order_data['store_url'] = $this->config->get('config_url');
        } else {
            if ($this->request->server['HTTPS']) {
                $order_data['store_url'] = HTTPS_SERVER;
            } else {
                $order_data['store_url'] = HTTP_SERVER;
            }
        }

        $json['success'] = $this->language->get('text_success_fast');

        $this->load->model('setting/setting');

        $from = $this->model_setting_setting->getSettingValue('config_email', 0);

        if (!$from) {
            $from = $this->config->get('config_email');
        }

        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        $mail->setTo($from);
        $mail->setFrom($from);
        $mail->setSender('Optom-sumka.ru');
        $mail->setSubject('Новый заказ с сайта ' . $_SERVER['HTTP_HOST']);
        $mail->setHtml($this->load->view('mail/quickorder_add', $order_data));
        $mail->send();
    //    $this->addNotify($order_data);
       $this->cart->clear();
       $this->response->addHeader('Content-Type: application/json');
    //    $data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);

    $this->response->addHeader('Content-Type: application/json');

    $json = [
        'success' => true,
        'message' => 'Операция выполнена успешно',
        'data' => []
    ];

    $this->response->setOutput(json_encode($json));
    $this->model_checkout_quickcheckout->addQuickOrder($order_data);
    }
}
