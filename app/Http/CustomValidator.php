<?php
	
	namespace App\Http;

	class CustomValidator{


		public function validateCreditCard( $attribute, $value, $parameters, $validator){

			$card = \CreditCard::validCreditCard($value);

			if( $card["valid"] == 1 ){
				return true;
			}else{
				return false;
			}
		}

		public function validateCVC( $attribute, $value, $parameters, $validator){

			if( sizeof( $parameters ) == 0 ){
				return false;
			}

			$cc = $parameters[0];

			$card = \CreditCard::validCreditCard( $cc );

			$validCvc = \CreditCard::validCvc($value, $card["type"] );

			return $validCvc;
		}

		public function validateCreditCardExpiration( $attribute, $value, $parameters, $validator){

			if( sizeof( $parameters ) == 0 ){
				return false;
			}

			$year = $parameters[0];

			$validDate = \CreditCard::validDate('20'.$year, $value);

			return $validDate;
		}

	}
?>