<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrichmentMock extends Model
{
    private $emailDomains = ["gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "aol.com", "icloud.com", "mail.com", "zoho.com", "gmx.com"];
    private $countries = ["US", "CA", "GB", "DE", "FR", "BR", "IN", "AU", "JP", "MX", "SV", "AR", "CL", "CO", "PE", "VE"];
    private $currencies = ["USD", "EUR", "GBP", "CAD", "AUD", "JPY", "MXN", "BRL", "INR", "CNY"];
    private $cardTypes = ["Visa", "MasterCard", "American Express", "Discover", "JCB", "Diners Club"];
    private $carrierStatuses = ["in_transit", "delivered", "out_for_delivery", "exception", "pending"];
    private $carriers = ["FedEx", "UPS", "DHL", "USPS", "Amazon Logistics", "Royal Mail", "Canada Post", "Australia Post", "Japan Post"];
    private $locations = ["New York, NY", "Los Angeles, CA", "Chicago, IL", "Houston, TX", "Phoenix, AZ", "Philadelphia, PA", "San Antonio, TX", "San Diego, CA", "Dallas, TX", "San Jose, CA"];

    public function get_form_provider_enrichment(): array{
       

        $data = [
                "email_domain" => $this->emailDomains[array_rand($this->emailDomains)],
                "geo_country" => $this->countries[array_rand($this->countries)],
                "risk_score" => random_int(1, 100),
                "lead_segment" => "SMB"
                ];

        return $data;   
        
    }

    public function get_payment_gateway_enrichment(): array{
        $data = [
                "payment_method" => "credit_card",
                "card_type" => $this->cardTypes[array_rand($this->cardTypes)],
                "currency" => $this->currencies[array_rand($this->currencies)],
                "card_country" => $this->countries[array_rand($this->countries)],
                "payment_risk_score" => random_int(1, 100),
                ];

        return $data;   
        
    }


    public function get_status_tracker_enrichment(): array{
        $data = [
                "estimated_delivery" => "2024-06-10T12:00:00Z",
                "current_location" => $this->locations[array_rand($this->locations)],
                "delivery_status" => $this->carrierStatuses[array_rand($this->carrierStatuses)],
                "carrier" => $this->carriers[array_rand($this->carriers)]
                ];  
        return $data;
    }
}
