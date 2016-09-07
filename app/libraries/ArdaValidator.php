<?php

/**
 * @author Arda Kilicdagi
 * @link http://arda.pw
*/

class ArdaValidator extends \Illuminate\Validation\Validator {

    //based on http://stackoverflow.com/a/174750/570763
    public function validateLuhn($attribute, $number, $parameters)
    {
        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number=preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length=strlen($number);
        $parity=$number_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit-=9;
                }
            }
            // Total up the digits
            $total+=$digit;
        }

      // If the total mod 10 equals 0, the number is valid
      return ($total % 10 == 0) ? TRUE : FALSE;
    }

    /* unneeded
    protected function replaceLuhn($message, $attribute, $rule, $parameters)
    {
        return str_replace(':luhn', $parameters[0], $message);
    }*/

}