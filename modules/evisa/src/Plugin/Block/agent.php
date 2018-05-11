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
        ];
    }
    /**
     * Get user name
     */
    public function getUserName($user) {
        if ($user->get('uid')->value < 1) {

            $user_information = 'Visitor';
        } else {
            $user_information = $user->getUsername();
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
                $user_information .= " Balance Rs. " . $amount;
            }
        }
        return $user_information;
    }

}
