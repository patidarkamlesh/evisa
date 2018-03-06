<?php

function send_email($key, $message, $params) {
    switch ($key) {
        case 'price_assign_update':
            $message['from'] = \Drupal::config('system.site')->get('mail');
            $message['subject'] = t('Kamles Test Subject');
            $message['body'][] = t('Dear Admin, <br /> Visa Price of @customer for @country @purpose @visatype has been updated from @oldprice to @price', array('@customer' => $params['customer_name'], '@country' => $params['country_name'], '@purpose' => $params['purpose_type'], '@visatype' => $params['visa_type'], '@oldprice' => $params['old_price'], '@price' => $params['price']));
            break;
        case 'approved_visa':
            $message['from'] = \Drupal::config('system.site')->get('mail');
            $message['subject'] = t('Your Visa has been Approved');
            $message['body'][] = t('Dear Agent, <br /> Your Visa has been <b>Approved</b> for Passport Number @passsport', array('@passsport' => 'N34434224'));
            break;
        case 'rejected_visa':
            $message['from'] = \Drupal::config('system.site')->get('mail');
            $message['subject'] = t('Kamles Test Subject');
            $message['body'][] = t('Dear Agent, <br /> Your Visa has been <b>Rejected</b> by visa authority for Passport Number @passsport', array('@passsport' => 'N34434224'));
            break;
    }
}
