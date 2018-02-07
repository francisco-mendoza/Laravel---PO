<?php
use \Yapo\BifrostService as BifrostService;
use Yapo\BulkLoad as BL;
use Yapo\Partner as Partner;
use Yapo\BlocketApiService as BlocketApiService;
require_once('cpanel_common.php');
require_once('StaticPartnerRulesConfig.php');
require_once('util.php');
require_once('autoload_lib.php');
class cpanel_module_partners extends cpanel_module
{
    private $partner;
    public function cpanel_module_partners()
    {
        global $session_read_only;
        $session_read_only = false;
        $this->config = array_copy(bconf_get($GLOBALS['BCONF'], 'controlpanel.modules.partners'));
        $this->init();
    }
    public function partners()
    {
        $this->display_results('controlpanel/partners/partners.html');
    }
    /*
     * Returns an array with all the partners parameters that are obtained from input fields in form
     */
    public function getPartnerFromForm() {
        $partnerArray = null;
        if (isset($_POST["keyName"])) {
            //Assign tab character when user checks in screen the tab separator option
            $delimiter =  (isset($_POST["tab"]) && $_POST['tab'] === "on") ?
                "\\t" : trim(sanitizeVar($_POST['delimiter'], 'string'));
            $rules = $iRules = "";
            if (!isset($_POST["transmit"]) || $_POST["transmit"] !== "on") {
                $rules = sanitizeVar($_POST['rules'], 'string', FILTER_FLAG_NO_ENCODE_QUOTES);
                $iRules = sanitizeVar($_POST['iRules'], 'string', FILTER_FLAG_NO_ENCODE_QUOTES);
            }
            //Check images connection data available
            $imgConnData = ""; $imgProtocol = "";
            if (isset($_POST["imgData"]) && $_POST["imgData"] === "on") {
                $imgConnData = sanitizeVar($_POST['imgConnData'], 'string', FILTER_FLAG_NO_ENCODE_QUOTES);
                $imgProtocol = sanitizeVar($_POST['imgProtocol'], 'string');
            }
            $partnerArray = array(
                'Name' => sanitizeVar($_POST['name'], 'string'),
                'Protocol' => sanitizeVar($_POST['protocol'], 'string'),
                'ImgProtocol' => $imgProtocol,
                'ConnData' => sanitizeVar($_POST['connData'], 'string', FILTER_FLAG_NO_ENCODE_QUOTES),
                'ImgConnData' => $imgConnData,
                'PathFile' => sanitizeVar($_POST['pathFile'], 'string'),
                'FileName' => sanitizeVar($_POST['fileName'], 'string'),
                'PathImg' => sanitizeVar($_POST['pathImg'], 'string'),
                'Delimiter' => $delimiter,
                'Rules' => $rules,
                'IRules' => $iRules,
                'ApiPoint' => sanitizeVar($_POST['apiPoint'], 'string'),
                'InsDelay' => sanitizeVar($_POST['insDelay'], 'string'),
                'MailTo' => sanitizeVar($_POST['mailTo'], 'string'),
                'KeyName' => sanitizeVar($_POST['keyName'], 'string'),
                'Market' => sanitizeVar($_POST['market'], 'string'),
                'RunTime' => sanitizeVar($_POST['runTime'], 'string')
            );
        }
        return $partnerArray;
    }
    /**
     * Creates the partner and associates BlocketApi and Bifrost services to it
     * @param $partnerArray object with params introduced by user
     * @param $bifrostService instance of the service that connects with Bifrost to save the partner in BD
     * @param $blocketApiService instance of Blocket Api Service
     * @throws exception when occurs an error in the partner creation in any of the services
     */
    public function createPartner($partnerArray, $bifrostService, $blocketApiService) {
        $partner = new Partner($partnerArray);
        $partner
            ->addService($bifrostService)
            ->addService($blocketApiService);
        $result = $partner->create();
        if (!$result) {
            throw new \Exception($partner->errorMsg);
        }
        return;
    }
    /*
     * Creates the partner and render results
     */
    public function addpartner()
    {
        $partnerArray = $this->getPartnerFromForm();

        if ($partnerArray != null) {
            try {
                $bifrostService = new BifrostService(new ProxyClient());
                $blocketApiService = new BlocketApiService('redis_mobile_api');
                if (empty($partnerArray['Rules'])) {
                    //Assign static rules from TransmitData
                    $rulesConfig = new StaticPartnerRulesConfig();
                    $partnerArray['Rules'] = $rulesConfig->getRules();
                    $partnerArray['IRules'] = $rulesConfig->getImageRules();
                }
                $this->createPartner($partnerArray, $bifrostService, $blocketApiService);
                $this->response->add_data("create_msg", lang('CREATE_PARTNER_OK'));
                $this->response->add_data("create_msg_class", 'msg-success');
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                if (strpos($msg, 'MISSING_PARAMETER') !== FALSE) {
                    list($msg,$parameter) = explode(" ", $msg);
                    $this->response->add_data("create_parameter", lang($parameter));
                }
                $this->response->add_data("create_msg", lang($msg));
                $this->response->add_data("create_msg_class", 'msg-error');
            }
        }
        $this->partners();
    }
    public function partnerdeleteads()
    {
        if (isset($_POST["partner_to_delete"])) {
            $trans = new bTransaction();
            $trans->add_data('partner', sanitizeVar($_POST["partner_to_delete"], 'string'));
            $trans->add_data('check', isset($_POST["just_check"]) && $_POST["just_check"] == "on" ? 1 : 0);
            $reply = $trans->send_command('partner_deleteads');
            if ($reply['status']=='TRANS_OK') {
                $this->response->add_data("delete_ad_id", $reply['ad_id']);
                $this->response->add_data("delete_external_ad_id", $reply['external_ad_id']);
            }
        }
        $this->partners();
    }
    public function editpartner()
    {
        $this->partners();
    }
    /*
     * Creates a Partner object, by parsing the parameter association made in CP.
     * To create the Partner object:
     * - A dictionary with conversion info has to be created for
     * every parameter received.
     * - A set of validators has to be created, by using the BL\BulkLoadRule object,
     * which associates a certain bconfigurated validator to a Yapo parameter.
     */
    public function addassociation()
    {
        $params = sanitizeVar($_POST['params'], 'array_string');
        $indexes = array();
        $dictionaries = array();
        /* Create index association matrix for user params */
        foreach($params as $i => $p) {
            $indexes[$p][] = $i;
            $partnerTranslation = NULL;
            if (isset($_POST['dict-hidden'][$i]) && !empty($_POST['dict-hidden'][$i])) {
                $data = sanitizeVar($_POST['dict-hidden'][$i], 'string');
                $data = explode(';', $data);
                $partnerTranslation = array();
                foreach ($data as $d) {
                    if ($d == '') {
                        continue;
                    }
                    list($k, $v) = explode(':', $d);
                    $partnerTranslation[$k] = $v;
                }
            }
            $dictionaries[$p] = $partnerTranslation;
        }
        /* Create index association matrix for mandatory params */
        $mandatoryFields = bconf_get($BCONF, '*.bulkload.mandatory.params');
        $mandatoryIndexes = array();
        foreach ($mandatoryFields as $mField => $v) {
            if (isset($indexes[$v])) {
                $mandatoryIndexes[$mField] = $indexes[$v];
            }
        }
        try {
            if (!array_key_exists('subject', $indexes)) {
                $subjectArr = $this->createSubject($indexes);
                if (!$subjectArr) {
                    throw new \Exception('PARTNER_MISSING_SUBJECT');
                }
                $indexes['subject'] = $subjectArr;
                $dictionaries['subject'] = array();
            }
            $bulkRules = new BL\BulkLoadRule();
            $this->addRules('*.bulkload_validator', $indexes, $dictionaries, $bulkRules);
            $this->addRules('*.bulkload_validator_mandatory', $mandatoryIndexes, NULL, $bulkRules);
            $bifrostService = new BifrostService(new ProxyClient());
            $blocketApiService = new BlocketApiService('redis_mobile_api');
            $partnerArray = array(
                'name' => $_SESSION['partnerName'],
                'market' => $_SESSION['parentCategory'],
                'rules' => $bulkRules->getJson(),
                'host' => $_SESSION['host'],
                'port' => $_SESSION['port'],
                'protocol' => $_SESSION['protocol'],
                'username' => $_SESSION['username'],
                'password' => $_SESSION['password'],
                'path' => $_SESSION['path']
            );
            $this->partner = new Partner($partnerArray);
            $this->partner
                ->addService($bifrostService)
                ->addService($blocketApiService);
            $result = $this->partner->create();
            if (!$result) {
                throw new \Exception($this->partner->errorMsg);
            }
            $this->response->add_data("create_msg", lang('CREATE_PARTNER_OK'));
            $this->response->add_data("create_msg_class", 'msg-success');
        } catch (\Exception $e) {
            $this->response->add_data("create_msg", lang($e->getMessage()));
            $this->response->add_data("create_msg_class", 'msg-error');
        }
        $this->partners();
    }
    private function createSubject($indexes)
    {
        if (!isset($indexes['location']) || !isset($indexes['operation'])) {
            return false;
        }
        return array_merge(
            $indexes['operation'],
            $indexes['location']
        );
    }
    public function toNumber($str)
    {
        return $str * 1;
    }
    /*
     * Adds rules according to a setting and the associations created by the user
     *
     * @param string $settingName name of the bconf setting to use
     * @param $association
     * @param $dictionaries
     * @param BL\BulkLoadRule object where the rules will be added to
     */
    private function addRules($settingName, $association, $dictionaries, BL\BulkLoadRule $bulkRules) {
        $validatorFactory = new BL\ValidatorFactory();
        foreach ($association as $paramName => $fields) {
            $setting = $this->getSetting($paramName, $settingName);
            $setting['ind'] = $fields;
            if ($dictionaries[$paramName]) {
                $setting['fun_arg'] = $dictionaries[$paramName];
            } else if (empty($setting['fun_arg'])) {
                $setting['fun_arg'] = NULL;
            } else if (isset($setting['fun_arg_type'])) {
                $setting['fun_arg'] = $this->evalValues($setting['fun_arg'], $setting['fun_arg_type']);
            }
            $validator = $validatorFactory->getValidator($setting);
            if (!empty($setting['extra_func'])) {
                $validator->applyFunction($setting['extra_func']);
            }
            $bulkRules->addRule($paramName, $validator);
        }
    }
    /*
     * Evaluates $values according to $type
     */
    private function evalValues($values, $type)
    {
        switch($type) {
            case 'array':
                return explode(',', $values);
            case 'array_num':
                return $values = array_map(array('self', 'toNumber'), explode(',', $values));
            case 'number':
                return $values * 1;
            default:
                return $values;
        }
    }
    /*
     * Returns the 'bulkload_validator' setting related to $param
     *
     * @param string $param Parameter used to retrieve the setting
     */
    private function getSetting($param = '', $settingName)
    {
        $bulk_settings = array('param' => $param, 'partner' => 'default');
        get_settings(
            bconf_get($BCONF, $settingName),
            "param",
            create_function('$s,$k,$d', 'return $d[$k];'),
            create_function('$s,$k,$v,$d', '$d[$k] = $v;'),
            $bulk_settings
        );
        return $bulk_settings;
    }
    public function main($function = NULL)
    {
        openlog("cpanel_partners", LOG_ODELAY, LOG_LOCAL0);
        if (!is_null($function)) {
            $this->response->fill_array('page_js', '/js/partners.js');
            $this->response->fill_array('page_css', '/css/partners.css');
            $this->run_method($function);
        } else {
            header('Location: '.$_SERVER['REQUEST_URI'].'&a=addpartner');
            die();
        }
    }
}