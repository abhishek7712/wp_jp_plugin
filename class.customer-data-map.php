<?php 

class Customer_Data_Map {

	protected $input = array();

	public function __construct($input = array()) {
		$this->input = $input;
	}
	/**
	 * map customer data for local database storage
	 * @return [array] [customer data]
	 */
	public function get_plugin_input() {
		$map = ['email', 'first_name', 'last_name', 'company_name', 'is_sync'];
		$addressFields = ['address','address_line_1','city','state_id','country_id','zip'];
		$data = $this->map_inputs($map);
		$address['address'] = $this->mapFirstSubInputs($addressFields, 'address');
		if(ine($this->input, 'same_as_customer_address')){
			$address['same_as_customer_address'] = true;
			$address['billing'] = $address['address'];
		} else {
			$address['billing']	= $this->mapFirstSubInputs($addressFields, 'billing');
			$address['same_as_customer_address'] = false;
		}
		$data['address'] = json_encode($address, true);
		$data['is_commercial'] = false ;
		if(ine($this->input, 'jobprogress_customer_type2')) {
			$data['is_commercial']  = true;
			$data['first_name']   = htmlentities($this->input['company_name_commercial']);
			//in commercial case company name and last name should be null
			$data['company_name']    = '';
			$data['last_name']     = '';
		}
		$data['phones'] = json_encode($this->map_phone_inputs(), true);
		$data['additional_emails'] = json_encode($this->map_additional_mail_input(), true);
		$data['created_at'] = current_time('mysql');
		$job = $this->map_job_input();
		$data['job'] = json_encode($job, true);
		return $data;
	}

	
	/**
     *  Map  Model fields to inputs
     *  @return array of mapped array fields.
     */
    private function mapFirstSubInputs($map, $inputKey){
    	$ret = array();
    	foreach ($map as $key => $value) {
			if(is_numeric($key)){
				$ret[$value] = isset($this->input[$inputKey][$value]) ? htmlentities($this->input[$inputKey][$value]) : "";
			}else{
				$ret[$key] = isset($this->input[$inputKey][$value]) ? htmlentities($this->input[$inputKey][$value]) : "";
			}
		}
        return $ret;
    }

    /**
     * map phone inputs
     * @return [array] [phones input]
     */
    private function map_phone_inputs() {
    	$phones = $this->input['phones'];
    	$ret = [];
    	foreach ($phones as $key => $phone) {
    		$ret[$key]['label'] = isset($phone['label']) ? htmlentities($phone['label']) : '';
    		$ret[$key]['number'] = isset($phone['number']) ? htmlentities($phone['number']) : '';
    		$ret[$key]['ext'] = isset($phone['ext']) ? htmlentities($phone['ext']) : '';
    	}

    	return $ret;
    }

    /**
     * map_additional mail input
     * @return [arrat] [additional email input]
     */
    private function map_additional_mail_input() {
    	if(! ine($this->input, 'additional_emails')) return false;
    	$ret = [];
    	$additional_emails = $this->input['additional_emails'];
    	foreach ($additional_emails as $key => $value) {
    		$ret[] = htmlentities($value);
    	}

    	return $ret;
    }

    private function map_job_input() {
    	$job = $this->input['job'];
    	$job['trades'] = $this->map_trade_inputs($job['trades']);
    	$job['description'] = htmlentities($job['description']);

    	return $job;

    }

    /**
	 * Map input
	 * @param  [array] $map [input map]
	 * @return [array]      [mapped input]
	 */
	private function map_inputs($map) {
		$ret = array();

    	// empty the set default.
    	if(empty($input)) {
    		$input = $this->input;
    	}

    	foreach ($map as $key => $value) {
			if(is_numeric($key)){
				$ret[$value] = isset($input[$value]) ? htmlentities($input[$value]) : "";
			}else{
				$ret[$key] = isset($input[$value]) ? htmlentities($input[$value]) : "";
			}
		}

        return $ret;
	}

	private function map_trade_inputs() {
		$trades = $this->input['job']['trades'];
		$ret = [];
    	foreach ($trades as $key => $value) {
    		$ret[] = htmlentities($value);
    	}
    	return $ret;
	}
}

