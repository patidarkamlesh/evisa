<?php

namespace Drupal\evisa\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use \Drupal\Core\Link;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EvisaController extends ControllerBase {

    public function sayhello() {
        return array(
            '#markup' => evisa_hello_world(),
        );
    }

    /**
     * Display list of visa countries with Add & Edit button
     */
    public function country() {
        // Get All Country
        $countries = getAllCountry();
        //create table header
        $header_table = [
            'country_name' => t('Country Name'),
            'opt' => t('Operation'),
        ];
        $rows = [];
        foreach ($countries as $country) {
            $edit = Url::fromUserInput('/evisa/country/form?country=' . $country->cid);
            $rows[] = [
                'country_name' => $country->country_name,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Country Link
        $countrydata['add_country'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/country/form">Add Country</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/country/form'>Add Country</a></p>",
        ];
        //display country table
        $countrydata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $countrydata;
    }

    /**
     * Display list of Nationalities with Add & Edit button
     */
    public function nationality() {
        // Get All Nationality
        $nationalities = getAllNationality();
        //create table header
        $header_table = [
            'country_name' => t('Nationality'),
            'opt' => t('Operation'),
        ];
        $rows = [];
        foreach ($nationalities as $nationality) {
            $edit = Url::fromUserInput('/evisa/national/form?national=' . $nationality->naid);
            $rows[] = [
                'nationality_name' => $nationality->nationality_name,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Nationality Link
        $nationaldata['add_nationality'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/national/form">Add Nationality</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/national/form'>Add Nationality</a></p>",
        ];
        //display Nationality table
        $nationaldata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $nationaldata;
    }
    /**
     * Display list of Purpose of Travel with Add & Edit button
     */
    public function purpose() {
        // Get All Nationality
        $purposes = getAllPurpose();
        //create table header
        $header_table = [
            'purpose_travel' => t('Purpose of Travel'),
            'opt' => t('Operation'),
        ];
        $rows = [];
        foreach ($purposes as $purpose) {
            $edit = Url::fromUserInput('/evisa/purpose/form?purpose=' . $purpose->pid);
            $rows[] = [
                'purpose_travel' => $purpose->purpose_travel,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Purpose of Travel Link
        $purposedata['add_purpose'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/purpose/form">Add Purpose of Travel</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/purpose/form'>Add Purpose of Travel</a></p>",
        ];
        //display Purpose of Travel table
        $purposedata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $purposedata;
    }
    /**
     * Display list of Type of Visa with Add & Edit button
     */
    public function visaType() {
        // Get All Visa Type
        $visatypes = getAllVisaType();
        //create table header
        $header_table = [
            'visa_type' => t('Type of Visa'),
            'opt' => t('Operation'),
        ];
        $rows = [];
        foreach ($visatypes as $visatype) {
            $edit = Url::fromUserInput('/evisa/visatype/form?type=' . $visatype->vtid);
            $rows[] = [
                'visa_type' => $visatype->visa_type,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Visa Type Link
        $visatypedata['add_visatype'] = [
            '#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/visatype/form">Add Type of Visa</a></p>',
        ];
        //display Visa Type table
        $visatypedata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $visatypedata;
    }
    /**
     * Display Association of Country & Purpose of Travel
     */
    public function countryPurpose() {
        //Get Country Purpose of Travel data 
        $countryPurposes = getAllCountryPurposes();
        //create table header
        $header_table = [
            'country_name' => t('Country'),
            'purpose' => t('Purpose of Travel'),
        ];
        $rows = [];
        foreach ($countryPurposes as $countryPurpose) {
            $rows[] = [
                'country_name' => $countryPurpose->country_name,
                'purpose' => $countryPurpose->purpose_travel,
            ];
        }
        // Add Visa Type Link
        $countrypurposedata['add_countrypurpose'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/countrypurpose/form">Add Association of Country & Purpose of Travel</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/countrypurpose/form'>Add Association of Country & Purpose of Travel</a></p>",
        ];
        //display Visa Type table
        $countrypurposedata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $countrypurposedata;
    }
    /**
     * Display Association of Country & Purpose of Travel
     */
    public function countryPurposeVisa() {
        //Get Country Purpose of Travel Visa type data 
        $countryPurposeVisas = getAllCountryPurposeVisa();
        //create table header
        $header_table = [
            'country_name' => t('Country'),
            'purpose' => t('Purpose of Travel'),
            'visa_type' => t('Visa type'),
        ];
        $rows = [];
        foreach ($countryPurposeVisas as $countryPurposeVisa) {
            $rows[] = [
                'country_name' => $countryPurposeVisa->country_name,
                'purpose' => $countryPurposeVisa->purpose_travel,
                'visa_type' => $countryPurposeVisa->visa_type,
            ];
        }
        // Add Visa Type Link
        $countrypurposeVisadata['add_countrypurpose'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/countrypurposevisa/form">Add Association</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/countrypurposevisa/form'>Add Association</a></p>",
        ];
        //display Visa Type table
        $countrypurposeVisadata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $countrypurposeVisadata;
        
    }
    /**
     * Display Association of Country & Purpose of Travel
     */
    public function priceAssignment() {
        //Get Country Purpose of Travel Visa type data 
        $priceAssignments = getPriceAssignment();
        //create table header
        $header_table = [
            'customer_name' => t('Customer Name'),
            'country_name' => t('Country'),
            'purpose' => t('Purpose of Travel'),
            'visa_type' => t('Visa type'),
            'price' => t('Price'),
            'opt' => t('Operation'),
        ];
        $rows = [];
        foreach ($priceAssignments as $priceAssignment) {
            $edit = Url::fromUserInput('/evisa/priceassignment/edit/' . $priceAssignment->id);
            $rows[] = [
                'customer_name' => $priceAssignment->customer_name,
                'country_name' => $priceAssignment->country_name,
                'purpose' => $priceAssignment->purpose_travel,
                'visa_type' => $priceAssignment->visa_type,
                'price' => $priceAssignment->price,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Price Assignment Link
        $priceAssigndata['add_priceassign'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/priceassignment/form">Add Price Assignment</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/priceassignment/form'>Add Price Assignment</a></p>",
        ];
        //display Visa Type table
        $priceAssigndata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $priceAssigndata;
    }
    /**
     * Send Email test
     */
    public function emailtest() {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'evisa';
        $key = 'test';
        $to = 'mail.kamleshpatidar@gmail.com';
        $params['message'] = 'Message Test';
        $langcode = '';
        $send = true;
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        if ($result['result'] !== true) {
            drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
        } else {
            drupal_set_message(t('Your message has been sent.'));
        }
    }
    /**
     * Account Page for all
     */
    public function viewVisa() {
        $visa_id = \Drupal::request()->get('vid');
        $visaDetail = visaDetail($visa_id);
        
        $photoUrl = getUpFileUrl($visaDetail['pas_photo_id']);
        $passFirstUrl = getUpFileUrl($visaDetail['pas_passport_first_id']);
        $passLastUrl = getUpFileUrl($visaDetail['pas_passport_last_id']);
        $supDoc1Url = getUpFileUrl($visaDetail['pas_sup_doc_1']);
        $supDoc2Url = getUpFileUrl($visaDetail['pas_sup_doc_2']);
        $ticketUrl = getUpFileUrl($visaDetail['pas_ticket']);
        
        return [
      '#theme' => 'view_visa',
      '#visa_detail' => $visaDetail,
      '#photo_url' => $photoUrl,
      '#pass_first' => $passFirstUrl,
      '#pass_last' => $passLastUrl,
      '#sup_doc_1' => $supDoc1Url,
      '#sup_doc_2' => $supDoc2Url,
      '#ticket' => $ticketUrl,
            
    ];
    }
    /**
     * Download Visa
     * @param INT $vid visa ID
     */
    public function downloadVisa() {
        $scheme = 'public';
        $approvalId = \Drupal::request()->get('vid');
        $newfile = \Drupal\file\Entity\File::load($approvalId);
        $uri = $newfile->getFileUri();
        $newfile->setFilename($newfile->getFilename());
        $downloadFile = evisa_file_download($newfile);
        $contentdis = 'attachment';
        $kam = 1;
        if ($kam == 1) {
            return new BinaryFileResponse($uri, 200, $downloadFile, $scheme !== 'private', $contentdis);
        } else {
            $price_detail = '';
            return [
                '#theme' => 'download_visa',
                '#price_detail' => $price_detail,
            ];
        }
    }
    /**
     * Block Customer List
     */
    public function blockCustomerList() {
        $blockCustomers = getBlockedCustomer();
        //create table header
        $header_table = [
            'customer_name' => t('Customer Name'),
            'block_date' => t('Block Date'),
            'opt' => t('Action'),
        ];
        $rows = [];
        foreach ($blockCustomers as $blockCustomer) {
            $edit = Url::fromUserInput('/evisa/blockCust/unblock/' . $blockCustomer->id, ['attributes' => ['class' => 'button']]);
            $rows[] = [
                'customer_name' => $blockCustomer->customer_name,
                'block_date' => date('d-m-Y', $blockCustomer->block_date),
                'opt' => Link::fromTextAndUrl('Unblock', $edit)
            ];
        }
        // Add Block Customer Link
        $blockcustdata['block_customer'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/blockCust/form">Block Customer</a></p>',
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/evisa/blockCust/form'>Block Customer</a></p>",
        ];
        //display Visa Type table
        $blockcustdata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $blockcustdata;
    }
    /**
     * Download Visa Report
     */
    public function downloadVisaReport() {
        $visaReportUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $queryString = parse_url($visaReportUrl);
        if(!empty($queryString['query'])) {
         parse_str($queryString['query'], $output);  
         if(array_key_exists('page', $output)) {
             unset($output['page']);
         }
        }
        $query = \Drupal::database()->select('visa_report', 'vr');
        $query->join('visa', 'v', 'v.id = vr.visa_id');
        $query->fields('vr', ['id','visa_id','customer_name','destination_name','purpose_name','visa_type_name','nationality','visa_price','urgent','name','passport_no', 'father_name', 'mother_name', 'created', 'status_id', 'approved_visa']);
        if (isset($output['customer_name']) && (!empty($output['customer_name']))){
          $query->condition('vr.customer_name', '%' . db_like($output['customer_name']) . '%', 'LIKE');
        }
        $roles = \Drupal::currentUser()->getRoles();
        if(in_array('agent', $roles)){
          $query->condition('v.customer_id', getCustomerId());
        }
        $visaReports = $query->execute()->fetchAll();
        //print_r($visaReports); exit;

$header = array(
		t('Node ID'),
		t('Node Title'),
		t('Node Type'),
  );  
$xls_content = '';
$xls_content_row = '';
$data =  array();
		$data[] = 1;
		$data[] = 'kamlesh Title';
		$data[] = 'Type';
		$xls_content_row .= implode("\t", array_values($data)) . "\r\n";
$xls_content_header = implode("\t", array_values($header)) . "\r";
  $xls_content .= $xls_content_header . "\n" . $xls_content_row;
       header("Expires: 0");
       header("Pragma: public");
       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       header('Content-type: application/ms-excel');
       header('Content-Disposition: attachment;filename=node_listing.xls');
       print $xls_content;
       exit();  
                
    }
}
