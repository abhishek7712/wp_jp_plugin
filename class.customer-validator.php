<?php 
class Customer_Validator {

	public $validation_error;

	public function is_valid() {
		$is_commercial = 0;
		$hasError = false;
		$error = new WP_Error;

		if(ine($_POST, 'jobprogress_customer_type2')) {
			$is_commercial = 1;
		}

		if(!isset($_POST['jobprogress_customer_type1']) && !isset($_POST['jobprogress_customer_type2'])) {
			$error->add('customer_type', 'This field is required.');
			$hasError = true;
		}

		if( ($is_commercial) && ( sanitize_text_field($_POST['company_name_commercial']) === '') ) {
			$error->add('company_name_commercial', 'Please enter the company name.');
			$hasError = true;
		} 
		if (! $is_commercial && (sanitize_text_field($_POST['first_name']) === '')) {
			$error->add('first_name', 'Please enter the first name.');
			$hasError = true;
		}
		if (! $is_commercial && (sanitize_text_field($_POST['last_name']) === '')) {
			$error->add('last_name', 'Please enter the last name.');
			$hasError = true;
		} 
		 if(sanitize_text_field($_POST['email']) === '' ) {
			$error->add('email', 'Please enter the email.');
			$hasError = true;
		} else {
			if(! filter_var(sanitize_text_field($_POST['email']), FILTER_VALIDATE_EMAIL))	{
				$error->add('email', 'The email must be a valid email address.');
				$hasError = true;
			}
		}
		

		if(isset($_POST['additional_emails']) 
			&& !empty($additional_emails = $_POST['additional_emails']) ) {
			foreach ($additional_emails as $key => $additional_email) {
				if(! $additional_email) {
					$error->add("additional_emails.$key", 'Please enter the additional email.');
					$hasError = true;
					continue;
				}
				if(! filter_var($additional_email, FILTER_VALIDATE_EMAIL))	{
					$error->add("additional_emails.$key", 'The additional email must be a valid email 
					address.');
					$hasError = true;
				}
			}
		}

		if(ine($_POST, 'job')) {
			if(! ine($_POST['job'], 'trades')) {
				$error->add("job_trades", 'Please select the trades.');
				$hasError = true;
			}
			if(! ine($_POST['job'], 'description')) {
				$error->add("job_description", 'Please enter the description.');
				$hasError = true;
			}
		}

		if(count($_POST['phones'])) {
			$phones = array_filter($_POST['phones']);

			foreach ($phones as $key => $value) {

				if(! ine($value, 'label')) {
					$error->add("phones.$key.label", 'Please choose the phone label.');
					$hasError = true;
				}
				if(! ine($value, 'number')) {
					$error->add("phones.$key.number", 'This field is required.');
					$hasError = true;
					continue;
				}
				if(!is_numeric($value['number'])) {
					$error->add("phones.$key.number", 'The phone must be a number.');
					$hasError = true;
					continue;
				}
				if(strlen($value['number']) > 10) {
					$error->add("phones.$key.number", 'The phone number may not be greater than 10 digit.');
					$hasError = true;
				} 
				if(strlen($value['number']) < 10) {
					$error->add("phones.$key.number", 'The phone number may not be less than than 10 
					digit.');
					$hasError = true;
				}
			}
		}
		$this->validation_error = $error;
		if($hasError) {

			return false;
		}

		return true;
	}

	public function get_validation_error() {
		return $this->validation_error;
	}

	
}