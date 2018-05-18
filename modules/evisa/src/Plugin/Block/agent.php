<?php

namespace Drupal\evisa\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides Agent Account Information
 * 
 * @Block(
 *   id = "agent",
 *   admin_label = "Agent Information"
 * )
 */
class agent extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $user = User::load(\Drupal::currentUser()->id());
        return [
            //'#markup' => $this->getAgentInfo(),
            '#theme' => 'agent',
            '#user_name' => $this->getUserName($user),
            '#price_info' => $this->getPriceInfo($user),
            '#login' => $this->loginLink($user) 
        ];
    }
    /**
     * Get user name
     */
    public function getUserName($user) {
        $user_information = [];
        if ($user->get('uid')->value < 1) {
            $user_information['name'] = 'Visitor';
        } else {
            $user_information['name'] = $user->getUsername();
            $user_information['profile'] = $GLOBALS['base_url'].'/user';
        }
        return $user_information;
    }
    /**
     * Get Price Information
     */
    public function getPriceInfo($user) {
        $user_information = '';
        if ($user->get('uid')->value > 1) {

            $roles = $user->getRoles();
            if (in_array('agent', $roles)) {
                $customerId = getCustomerId();
                $amount = getCumAmount($customerId);
                if(!$amount) {
                   $amount = 0; 
                }
                $user_information .= " Balance (INR) " . $amount;
            }
        }
        return $user_information;
    }
    /**
     * Login Information
     */
    public function loginLink($user) {
        $login = [];
        if ($user->get('uid')->value < 1) {
            $login['title'] = 'Log in';
            $login['path'] = $GLOBALS['base_url'].'/user/login';
        } else {
            $login['title'] = 'Log Out';
            $login['path'] = $GLOBALS['base_url'].'/user/logout';
        }
        
        return $login;
    }
}
