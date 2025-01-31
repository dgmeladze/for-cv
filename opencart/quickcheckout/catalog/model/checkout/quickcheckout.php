<?php
class ModelCheckoutQuickcheckout extends Model {
    public function addQuickOrder($data) {
        $order_id = $this->createOrder($data);
        $maxRetries = 3;
        $retryDelay = 5;
        $missedOrdersDir = DIR_ROOT . 'missed_orders/';
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $this->addOrderProducts($data['products'], $order_id);
                $this->addNotify($data, $order_id, 1);
                return;
            } catch (Exception $e) {
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                } else {
                    if (!is_dir($missedOrdersDir)) {
                        mkdir($missedOrdersDir, 0755, true);
                    }
                    $filename = $missedOrdersDir . 'order_' . time() . '.json';
                    file_put_contents($filename, json_encode(['order_id' => $order_id, 'products' => $data['products']], JSON_PRETTY_PRINT));
                    throw $e;
                }
            }
        }
    }

    private function createOrder($data) {
        $address_components = array($data['address_1']);

        if (!empty($data['building'])) {
            $address_components[] = 'дом ' . $data['building'];
        }

        if (!empty($data['entrance'])) {
            $address_components[] = 'подъезд/вход ' . $data['entrance'];
        }

        if (!empty($data['appartment'])) {
            $address_components[] = 'кв./офис' . $data['appartment'];
        }

        $payment_address_1 = implode(', ', $address_components);

        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET
            store_id = '" . (int)$data['store_id'] . "',
            store_name = '" . $this->db->escape($data['store_name']) . "',
            store_url = '" . $this->db->escape($data['store_url']) . "',
            telephone = '" . $this->db->escape($data['telephone']) . "',
            email = '" . $this->db->escape($data['email']) . "',
            firstname = '" . $this->db->escape($data['firstname']) . "',
            customer_id = '" . (int)$data['customer_id'] . "',
            customer_group_id = '" . (int)$data['customer_group_id'] . "',
            total = '" . (float)$data['total'] . "',
            shipping_city = '" . $this->db->escape($data['city']) . "',
            payment_city = '" . $this->db->escape($data['city']) . "',
            shipping_address_1 = '" . $this->db->escape($payment_address_1) . "',
            payment_address_1 = '" . $this->db->escape($payment_address_1) . "',
            shipping_postcode = '" . $this->db->escape($data['postcode']) . "',
            payment_postcode = '" . $this->db->escape($data['postcode']) . "',
            shipping_method = '" . $this->db->escape($data['shipping_method']) . "',
            shipping_code = '" . $this->db->escape($data['shipping_code']) . "',
            payment_method = '" . $this->db->escape($data['payment_method']) . "',
            payment_code = '" . $this->db->escape($data['payment_code']) . "',
            language_id = '1',
            currency_id = '" . (int)$data['currency_id'] . "',
            currency_code = '" . $this->db->escape($data['currency_code']) . "',
            currency_value = '" . (float)$data['currency_value'] . "',
            ip = '" . $this->db->escape($data['ip']) . "',
            order_status_id = '" . (int)$data['order_status_id'] . "',
            date_added = NOW(),
            date_modified = NOW()"
        );
        $order_id = $this->db->getLastId();
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $invoice_prefix = 'INV_' . $year . '_' . $month . '_';
        $invoice_no = $day . '_' . $order_id;

        $this->db->query("UPDATE oc_order SET 
                    invoice_prefix = '" . $this->db->escape($invoice_prefix) . "', 
                    invoice_no = '" . $this->db->escape($invoice_no) . "' 
                    WHERE order_id = '" . (int)$order_id . "'");

        $this->db->query("INSERT INTO " . DB_PREFIX . "order_simple_fields SET
            order_id = '" . (int)$order_id . "',
            metadata = 'shipping_field30,shipping_field31,shipping_field32',
            shipping_field30 = '" . $this->db->escape($data['shipping_field30']) . "',
            shipping_field31 = '" . $this->db->escape($data['shipping_field31']) . "',
            shipping_field32 = '" . $this->db->escape($data['shipping_field32']) . "',
            payment_field30 = '" . $this->db->escape($data['shipping_field30']) . "',
            payment_field31 = '" . $this->db->escape($data['shipping_field31']) . "',
            payment_field32 = '" . $this->db->escape($data['shipping_field32']) . "'"
        );
        return $order_id;
    }

    private function addOrderProducts($data, $order_id) {
        foreach ($data as $product) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET
                order_id = '" . (int)$order_id . "',
                product_id = '" . (int)$product['product_id'] . "',
                name = '" . $this->db->escape($product['name']) . "',
                model = '" . $this->db->escape($product['model']) . "',
                quantity = '" . (int)$product['quantity'] . "',
                price = '" . (float)$product['price'] . "',
                total = '" . (float)$product['total'] . "',
                tax = '',
                reward = ''"
            );
        }
    }
    public function getAddresses() {
        $address_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");

        foreach ($query->rows as $result) {
            $simple_fields_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address_simple_fields WHERE address_id = '" . (int)$result['address_id'] . "'");

            $simple_fields = $simple_fields_query->num_rows ? $simple_fields_query->row : array();

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");

            if ($country_query->num_rows) {
                $country = $country_query->row['name'];
                $iso_code_2 = $country_query->row['iso_code_2'];
                $iso_code_3 = $country_query->row['iso_code_3'];
                $address_format = $country_query->row['address_format'];
            } else {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");

            if ($zone_query->num_rows) {
                $zone = $zone_query->row['name'];
                $zone_code = $zone_query->row['code'];
            } else {
                $zone = '';
                $zone_code = '';
            }

            $address_data[$result['address_id']] = array(
                'address_id'     => $result['address_id'],
                'firstname'      => $result['firstname'],
                'lastname'       => $result['lastname'],
                'company'        => $result['company'],
                'address_1'      => $result['address_1'],
                'address_2'      => $result['address_2'],
                'postcode'       => $result['postcode'],
                'city'           => $result['city'],
                'zone_id'        => $result['zone_id'],
                'zone'           => $zone,
                'zone_code'      => $zone_code,
                'country_id'     => $result['country_id'],
                'country'        => $country,
                'iso_code_2'     => $iso_code_2,
                'iso_code_3'     => $iso_code_3,
                'address_format' => $address_format,
                'custom_field'   => json_decode($result['custom_field'], true),
                'building'  => $simple_fields['field30'],
                'entrance'  => $simple_fields['field31'],
                'appartment'  => $simple_fields['field32'],
            );
        }

        return $address_data;
    }
    public function addNotify($order_info, $order_id, $order_status_id = 1) {
        $this->load->language('mail/order_add');

        $data['title'] = sprintf($this->language->get('text_subject'), $order_info['store_name'], $order_info['order_id']);
        $data['text_greeting'] = sprintf($this->language->get('text_greeting'), $order_info['store_name']);
        $data['text_order_detail'] = $this->language->get('text_order_detail');
        $data['text_order_id'] = $this->language->get('text_order_id');
        $data['text_date_added'] = $this->language->get('text_date_added');
        $data['text_payment_method'] = $this->language->get('text_payment_method');
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_ip'] = $this->language->get('text_ip');
        $data['text_order_status'] = $this->language->get('text_order_status');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_model'] = $this->language->get('text_model');
        $data['text_quantity'] = $this->language->get('text_quantity');
        $data['text_price'] = $this->language->get('text_price');
        $data['text_total'] = $this->language->get('text_total');
        $data['text_footer'] = $this->language->get('text_footer');

        $data['logo'] = $order_info['store_url'] . 'image/' . $this->config->get('config_logo');
        $data['store_name'] = $order_info['store_name'];
        $data['store_url'] = $order_info['store_url'];
        $data['customer_id'] = $order_info['customer_id'];
        $data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];
        $data['order_id'] = $order_info['order_id'];
        $data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
        $data['payment_method'] = $order_info['payment_method'];
        $data['shipping_method'] = $order_info['shipping_method'];
        $data['email'] = $order_info['email'];
        $data['telephone'] = $order_info['telephone'];
        $data['ip'] = $order_info['ip'];

        $order_status_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
        $data['order_status'] = $order_status_query->num_rows ? $order_status_query->row['name'] : '';

        $data['comment'] = isset($order_info['comment']) ? nl2br($order_info['comment']) : '';
        $address_components = array($order_info['city'], $order_info['address_1']);

        if (!empty($order_info['building'])) {
            $address_components[] = 'дом ' . $order_info['building'];
        }

        if (!empty($order_info['entrance'])) {
            $address_components[] = 'подъезд/вход ' . $order_info['entrance'];
        }

        if (!empty($order_info['appartment'])) {
            $address_components[] = 'кв./офис ' . $order_info['appartment'];
        }

        $payment_address_1 = implode(', ', $address_components);

        $data['payment_address'] = $payment_address_1;
        $data['shipping_address'] = $payment_address_1;

        $data['products'] = $order_info['products'];
        $data['totals'] = $order_info['total'];

        $from = $this->config->get('config_email');
        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        $mail->setTo($order_info['email']);
        $mail->setFrom($from);
        $mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $order_info['store_name'], $order_id), ENT_QUOTES, 'UTF-8'));
        $mail->setHtml($this->load->view('mail/new_order_add', $data));
        $mail->send();
    }
}