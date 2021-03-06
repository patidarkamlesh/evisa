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
use Drupal\node\Entity\Node;

class EvisaController extends ControllerBase {

    public function masterPage() {
        $menuItems = [];
        if(\Drupal::currentUser()->hasPermission('view country')) {
            $menuItem['url'] = 'eadmin/country';
            $menuItem['title'] = 'Country';
            array_push($menuItems,$menuItem);
        }
        
        if(\Drupal::currentUser()->hasPermission('view nationality')) {
            $menuItem['url'] = 'eadmin/nationality';
            $menuItem['title'] = 'Nationality';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view purpose')) {
            $menuItem['url'] = 'eadmin/purpose';
            $menuItem['title'] = 'Purpose of Travel';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view visatype')) {
            $menuItem['url'] = 'eadmin/visatype';
            $menuItem['title'] = 'Type of Visa';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view countrypurpose')) {
            $menuItem['url'] = 'eadmin/countrypurpose';
            $menuItem['title'] = 'Country Purpose of Travel Association';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view countrypurposevisa')) {
            $menuItem['url'] = 'eadmin/countrypurposevisa';
            $menuItem['title'] = 'Country Purpose of Travel Visa Type Association';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view priceassignment')) {
            $menuItem['url'] = 'eadmin/priceassignment';
            $menuItem['title'] = 'Price Assignment for Customer';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view account')) {
            $menuItem['url'] = 'eadmin/account';
            $menuItem['title'] = 'Account Transaction';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('block customer')) {
            $menuItem['url'] = 'eadmin/blockCust';
            $menuItem['title'] = 'Block Customer';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view visa document')) {
            $menuItem['url'] = 'eadmin/documentvisa';
            $menuItem['title'] = 'Visa Document';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view roe')) {
            $menuItem['url'] = 'eadmin/roe';
            $menuItem['title'] = 'Rate of Exchange';
            array_push($menuItems,$menuItem);
        }
        if(\Drupal::currentUser()->hasPermission('view vendor')) {
            $menuItem['url'] = 'eadmin/vendor';
            $menuItem['title'] = 'Vendor';
            array_push($menuItems,$menuItem);
        }
        return [
          '#theme' => 'master_page',
          '#menuItems' => $menuItems  
        ];
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
            $edit = Url::fromUserInput('/eadmin/country/form?country=' . $country->cid);
            $rows[] = [
                'country_name' => $country->country_name,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Country Link
        $countrydata['add_country'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/country/form'>Add Country</a>",
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
            $edit = Url::fromUserInput('/eadmin/national/form?national=' . $nationality->naid);
            $rows[] = [
                'nationality_name' => $nationality->nationality_name,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Nationality Link
        $nationaldata['add_nationality'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/national/form'>Add Nationality</a>",
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
            $edit = Url::fromUserInput('/eadmin/purpose/form?purpose=' . $purpose->pid);
            $rows[] = [
                'purpose_travel' => $purpose->purpose_travel,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Purpose of Travel Link
        $purposedata['add_purpose'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/purpose/form'>Add Purpose of Travel</a>",
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
            $edit = Url::fromUserInput('/eadmin/visatype/form?type=' . $visatype->vtid);
            $rows[] = [
                'visa_type' => $visatype->visa_type,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Visa Type Link
        $visatypedata['add_visatype'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/visatype/form'>Add Type of Visa</a>",
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
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/countrypurpose/form'>Add Association of Country & Purpose of Travel</a>",
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
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/countrypurposevisa/form'>Add Association</a></p>",
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
            $edit = Url::fromUserInput('/eadmin/priceassignment/edit/' . $priceAssignment->id);
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
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/priceassignment/form'>Add Price Assignment</a>",
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
     * View Visa Page for Customers
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
        $referUrl = \Drupal::request()->server->get('HTTP_REFERER'); 
        
        return [
            '#theme' => 'view_visa',
            '#visa_detail' => $visaDetail,
            '#photo_url' => $photoUrl,
            '#pass_first' => $passFirstUrl,
            '#pass_last' => $passLastUrl,
            '#sup_doc_1' => $supDoc1Url,
            '#sup_doc_2' => $supDoc2Url,
            '#ticket' => $ticketUrl,
            '#express' => (strpos($referUrl, 'express') ? 1 : 0),
        ];
    }

    /**
     * Download Visa
     * @param INT $vid visa ID
     */
    public function downloadVisa() {
        $scheme = 'public';
        $roles = \Drupal::currentUser()->getRoles();
        $approvalId = \Drupal::request()->get('vid');
        $newfile = \Drupal\file\Entity\File::load($approvalId);
        $uri = $newfile->getFileUri();
        $newfile->setFilename($newfile->getFilename());
        $downloadFile = evisa_file_download($newfile);
        $contentdis = 'attachment';
        if(in_array('agent', $roles)){
          $blockStatus = getBlockedStatus(getCustomerId());
        } else {
          $blockStatus = FALSE;  
        }
        if ($blockStatus) {
            $price_detail = '';
            return [
                '#theme' => 'download_visa',
                '#price_detail' => $price_detail,
            ];
        } else {
            return new BinaryFileResponse($uri, 200, $downloadFile, $scheme !== 'private', $contentdis);
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
            $edit = Url::fromUserInput('/eadmin/blockCust/unblock/' . $blockCustomer->id, ['attributes' => ['class' => 'button']]);
            $rows[] = [
                'customer_name' => $blockCustomer->customer_name,
                'block_date' => date('d-m-Y', strtotime($blockCustomer->block_date)),
                'opt' => Link::fromTextAndUrl('Unblock', $edit)
            ];
        }
        // Add Block Customer Link
        $blockcustdata['block_customer'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/blockCust/form">Block Customer</a></p>',
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/blockCust/form'>Block Customer</a>",
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
     * Download MIS Report
     */
    public function downloadMisReport() {
        $roles = \Drupal::currentUser()->getRoles();
        $misReportUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $queryString = parse_url($misReportUrl);
        if (!empty($queryString['query'])) {
            parse_str($queryString['query'], $output);
            if (array_key_exists('page', $output)) {
                unset($output['page']);
            }
        }
        $query = \Drupal::database()->select('account_txn', 'ac');
        $query->leftJoin('visa_report', 'vr', 'ac.visa_id = vr.visa_id');
        $query->join('node_field_data', 'nf', 'nf.nid = ac.customer_id');
        $query->fields('ac', ['id', 'visa_id', 'debit', 'credit', 'txn_date', 'cum_amount', 'txn_reason', 'txn_type']);
        $query->fields('vr', ['id', 'visa_id', 'customer_name', 'customer_id', 'destination_name', 'purpose_name', 'visa_type_name', 'nationality', 'visa_price', 'urgent', 'name', 'passport_no', 'app_ref']);
        $query->fields('nf', ['title']);
        if (!empty($output['customer_id'])) {
            $query->condition('ac.customer_id', $output['customer_id']);
        }
        if (in_array('agent', $roles)) {
            $query->condition('ac.customer_id', getCustomerId());
        }
        if (!empty($output['fd'])) {
            $query->condition('ac.txn_date', $output['fd'], '>=');
        }
        if (!empty($output['td'])) {
            $query->condition('ac.txn_date', $output['td'], '<=');
        }
        $query->orderBy('ac.id', 'ASC');
        $misReports = $query->execute()->fetchAll();
        $misHeaders = [
            t('Bill Date'),
            t('Bill No'),
            t('Application Ref No'),
            t('Pax Name'),
            t('Visa Passport'),
            t('Transaction Remark'),
            t('Visa Country'),
            t('Visa Category'),
            t('Visa Type'),
            t('Dr'),
            t('Cr'),
            t('Balance'),
        ];
        $reportData = "";
        if (in_array('agent', $roles)) {
            $customer_name = $misReports[0]->title;
            $reportData .= "<div style='text-align:center;'>" . "VISA RECO" . "</div>";
            $reportData .= "<div style='text-align:center;'>" . "For The Period 24 February 2018 To 26 February 2018" . "</div>";
            $reportData .= "<div style='text-align:center;'>" . $customer_name . "</div>";
        }
        $reportData .= "<table style='border:2px solid black;'>";
        $reportData .= "<tr>";
        foreach ($misHeaders as $misHeader) {
            $reportData .= "<th style='border: 1px solid black; background-color:#0083C1;'>" . $misHeader . "</th>";
        }
        $reportData .= "</tr>";
        foreach ($misReports as $misReport) {
            $reportData .= "<tr>";
            $reportData .= "<td style='border: 1px solid black;'>" . date('d-M-Y', strtotime($misReport->txn_date)) . "</td>";
            $bill_no = ($misReport->txn_type == 'D') ? 'VS /' . $misReport->id : '';
            $reportData .= "<td style='border: 1px solid black;'>" . $bill_no . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->app_ref . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->name . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->passport_no . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->txn_reason . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->destination_name . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->purpose_name . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->visa_type_name . "</td>";
            $debit = !empty($misReport->debit) ? $misReport->debit : '';
            $reportData .= "<td style='border: 1px solid black;'>" . $debit . "</td>";
            $credit = !empty($misReport->credit) ? $misReport->credit : '';
            $reportData .= "<td style='border: 1px solid black;'>" . $credit . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $misReport->cum_amount . "</td>";
            $reportData .= "</tr>";
        }
        $reportData .= "</table>";
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename=mis_report.xls');
        header("Expires: 0");
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        print $reportData;
        exit();
    }

    /**
     * Download MIS Report
     */
    public function downloadSalesReport() {
        $roles = \Drupal::currentUser()->getRoles();
        $salesReportUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $queryString = parse_url($salesReportUrl);
        if (!empty($queryString['query'])) {
            parse_str($queryString['query'], $output);
            if (array_key_exists('page', $output)) {
                unset($output['page']);
            }
        }
        if(empty($output['fd']) && empty($output['td'])) {
            $output['fd'] = date('mm/dd/yyyy', strtotime('-1 month'));
            $output['td'] = date('mm/dd/yyyy');
        }
        //Get Sales Report Data
        $query = \Drupal::database()->select('visa', 'v');
        $query->join('node_field_data', 'nf', 'nf.nid = v.customer_id');
        $query->addField('nf', 'title', 'customer_name');
        $query->addField('v','customer_id', 'agent_id');
        $query->addExpression('count(v.id)', 'total_visa');
        $query->addExpression('sum(v.visa_price)', 'total_visa_price');
        $query->addExpression('max(v.created_date)', 'last_transaction');
        if (!empty($output['customer_id'])) {
            $query->condition('v.customer_id', $output['customer_id']);
        }
        if (!empty($output['fd'])) {
            $query->condition('v.created_date', $output['fd'], '>=');
        }
        if (!empty($output['td'])) {
            $query->condition('v.created_date', $output['td'], '<=');
        }
        if (in_array('sales_user', $roles)) {
            $query->condition('v.customer_id', getSalesCustomer(), 'IN');
        }
        $query->groupBy('v.customer_id');
        $query->groupBy('nf.title');
        $salesReports = $query->execute()->fetchAll();
        $agentIds = [];
        foreach($salesReports as $salesReport) {
            $agentIds[] = $salesReport->agent_id;
        }
        if(count($agentIds)) {
          $cumAmountMulti = getCumAmountMultiple($agentIds);
        }
        $salesHeaders = [
            t('Key Agent Name'),
            t('Last Transaction Date'),
            t('Visa Count'),
            t('Total Business'),
            t('Current Balance'),
        ];
        $reportData = "";
        $reportData .= "<table style='border:2px solid black;'>";
        $reportData .= "<tr>";
        foreach ($salesHeaders as $salesHeader) {
            $reportData .= "<th style='border: 1px solid black; background-color:#0083C1;'>" . $salesHeader . "</th>";
        }
        $reportData .= "</tr>";
        foreach ($salesReports as $salesReport) {
            $reportData .= "<tr>";
            $reportData .= "<td style='border: 1px solid black;'>" . $salesReport->customer_name . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . date('d-M-Y', strtotime($salesReport->last_transaction)) . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $salesReport->total_visa . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $salesReport->total_visa_price . "</td>";
            $reportData .= "<td style='border: 1px solid black;'>" . $cumAmountMulti[$salesReport->agent_id] . "</td>";
            $reportData .= "</tr>";
        }
        $reportData .= "</table>";
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename=sales_report.xls');
        header("Expires: 0");
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        print $reportData;
        exit();
    }

    /**
     * View Document Visa
     * @return type Array visadocdata
     */
    public function documentVisa() {
        $visaDocs = getVisaDoc();
        //create table header
        $header_table = [
            'country_name' => t('Country Name'),
            'purpose' => t('Purpose'),
            'visa_type' => t('Visa Type'),
            'opt' => t('Action'),
        ];
        $rows = [];
        foreach ($visaDocs as $visaDoc) {
            $edit = Url::fromUserInput('/evisa/documentvisa/' . $visaDoc->id, ['attributes' => ['class' => 'button']]);
            $rows[] = [
                'country_name' => $visaDoc->country_name,
                'purpose' => $visaDoc->purpose_travel,
                'visa_type' => $visaDoc->visa_type,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
            ];
        }
        // Add Visa Document Link
        $visadocdata['visa_doc'] = [
            '#markup' => "<p><a href='" . $GLOBALS['base_url'] . "/evisa/documentvisa/form'>Add Document List</a></p>",
        ];
        //display Visa Type table
        $visadocdata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $visadocdata;
    }

    /**
     * Agent Home Page to track last five visa
     */
    public function agentHome() {
        $latestVisas = getLatestVisa();
        //create table header
        $header_table = [
            //'app_ref' => t('Application Ref'),
            'country_name' => t('Country Name'),
            'purpose_of_travel' => t('Purpose of Travel'),
            'visa_type' => t('Visa Type'),
            'passport_no' => t('Passport No'),
            'name' => t('Pax name'),
            'status_id' => t('Status'),
        ];
        $rows = [];
        $status = [1 => 'Open', 2=>'In Progress', 3=>'Approved', 4=> 'Rejected', 5=> 'Cancelled'];
        foreach ($latestVisas as $latestVisa) {
            $rows[] = [
                //'app_ref' => $latestVisa->app_ref,
                'country_name' => $latestVisa->destination_name,
                'purpose_of_travel' => $latestVisa->purpose_name,
                'visa_type' => $latestVisa->visa_type_name,
                'passport_no' => $latestVisa->passport_no,
                'name' => $latestVisa->name,
                'status_id' => $status[$latestVisa->status_id],
            ];
        }
        //display Visa Type table
        $latestvisadata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        // Add Visa Document Link
        $latestvisadata['track_visa'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/evisa/visa'>More Info</a>",
        ];
        return $latestvisadata;
    }
    /**
     * Rate of Exchange Table
     */
    public function roe() {
        $roes = getRoe();
        // Add Rate of Exchange
        $roedata['add_roe'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/roe/form'>Add Rate of Exchange</a>",
        ];
        //create table header
        $header_table = [
            //'app_ref' => t('Application Ref'),
            'country_name' => t('Country Name'),
            'roe' => t('Rate of Exchange'),
            'opt' => t('Action'),
        ];
        $rows = [];
        foreach ($roes as $roe) {
            $edit = Url::fromUserInput('/eadmin/roe/form?rid=' . $roe->rid);
            $rows[] = [
                'country_name' => $roe->country_name,
                'roe' => $roe->roe,
                'opt' => Link::fromTextAndUrl('Update', $edit)
            ];
        }
        //display Rate of Exchange table
        $roedata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $roedata;
    }
    /**
     * Download Invoice in PDF format
     */
    public function downloadInvoice($vrid) {
        if (empty($vrid)) {
            return false;
        }
        //Get Invoice data
        $visaDetails =  visaDetail($vrid);
        //print_r($visaDetails); exit;
        $pdf = $this->generateInvoicePdf($visaDetails);
        $fileName = $visaDetails['passport_no'].'.pdf';

        // Tell the browser that this is not an HTML file to show, but a pdf file to
        // download.
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($pdf));
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        print $pdf;
        return [];
    }
    /**
     * Create Invoice PDF
     */
    protected function generateInvoicePdf($visaDetails) {
    // Get the content we want to convert into pdf.
    $f = new \NumberFormatter("en_IN", \NumberFormatter::SPELLOUT); 
    $priceInWord = $f->format($visaDetails['visa_price']);
    $custInfo = Node::load($visaDetails['customer_id']);
    $html_template = [
      '#theme' => 'invoice_pdf',
      '#visaDetails' => $visaDetails,
      '#priceInWord' => $priceInWord,
      '#custInfo' => $custInfo  
    ];
    $html = \Drupal::service('renderer')->render($html_template);

    // Never make an instance of TCPDF or TCPDFDrupal classes manually.
    // Use tcpdf_get_instance() instead.
    $tcpdf = tcpdf_get_instance();
    /* DrupalInitialize() is an extra method added to TCPDFDrupal that initializes
    *  some TCPDF variables (like font types), and makes possible to change the
    *  default header or footer without creating a new class.
    */
    $tcpdf->DrupalInitialize(array(
      'footer' => array(
        //'html' => 'This is a test!! <em>Bottom of the page</em>',
      ),
      'header' => array(
        /*'callback' => array(
          'function' => 'tcpdf_example_default_header',
          // You can pass extra data to your callback.
          'context' => array(
            'welcome_message' => 'Hello, tcpdf example!',
          ),
        ),*/
      ),
    ));
    // Insert the content. Note that DrupalInitialize automatically adds the first
    // page to the pdf document.
    $tcpdf->writeHTML($html);

    return $tcpdf->Output('', 'S');        
    }
    /**
     * Vendor Table
     */
    public function vendor() {
        $vendors = getVendor();
        // Add Rate of Exchange
        $vendordata['add_vendor'] = [
            '#markup' => "<a class='btn btn-primary' href='" . $GLOBALS['base_url'] . "/eadmin/vendor/form'>Add Vendor</a>",
        ];
        //create table header
        $header_table = [
            'vendor_name' => t('Vendor Name'),
            'opt' => t('Action'),
        ];
        $rows = [];
        foreach ($vendors as $vendor) {
            $edit = Url::fromUserInput('/eadmin/vendor/form?vdid=' . $vendor->vdid);
            $rows[] = [
                'vendor_name' => $vendor->vendor_name,
                'opt' => Link::fromTextAndUrl('Update', $edit)
            ];
        }
        //display Vendor table
        $vendordata['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        return $vendordata;
    }
    
    
}
