<?php

class ZugferdGenerator
{
	protected $doc;
	protected $profile;

	public function __construct($profile = 'comfort') {
		$this->profile = $profile;
	}

	public function generate($invoice, $items)
	{
		$this->doc = new DOMDocument('1.0', 'UTF-8');
		$this->doc->formatOutput = true;

		$rsm = $this->doc->createElement('rsm:CrossIndustryInvoice');
		$rsm->setAttribute('xmlns:rsm', 'urn:ferd:CrossIndustryDocument:invoice:2p1:' . $this->profile);
		$rsm->setAttribute('xmlns:ram', 'urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100');
		$rsm->setAttribute('xmlns:udt', 'urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100');

		$rsm->appendChild($this->createDocumentContext());
		$rsm->appendChild($this->createDocument($invoice));
		$rsm->appendChild($this->createTradeTransaction($items));
		$rsm->appendChild($this->createParty('ram:SellerTradeParty', $invoice->user_name, $invoice->user_address_1, $invoice->user_zip, $invoice->user_city, $invoice->user_country, $invoice->user_vat_id));
		$rsm->appendChild($this->createParty('ram:BuyerTradeParty', $invoice->client_name, $invoice->client_address_1, $invoice->client_zip, $invoice->client_city, $invoice->client_country));
		$rsm->appendChild($this->createSettlement($invoice));

		$this->doc->appendChild($rsm);

		$xml = $this->doc->saveXML();
		file_put_contents(UPLOADS_TEMP_FOLDER . 'zugferd-debug.xml', $xml);
		return $xml;
	}

	private function createDocumentContext() {
		$context = $this->doc->createElement('ram:ExchangedDocumentContext');
		$guideline = $this->doc->createElement('ram:GuidelineSpecifiedDocumentContextParameter');
		$guideline->appendChild($this->createElementWithValue('ram:ID', 'urn:ferd:guideline:zugferd:2p1:' . $this->profile));
		$context->appendChild($guideline);
		return $context;
	}

	private function createDocument($invoice) {
		$document = $this->doc->createElement('ram:ExchangedDocument');
		$document->appendChild($this->createElementWithValue('ram:ID', htmlspecialchars($invoice->invoice_number)));
		$document->appendChild($this->createElementWithValue('ram:TypeCode', '380'));
		$document->appendChild($this->createElementWithValue('ram:Name', 'RECHNUNG'));
		$document->appendChild($this->createElementWithValue('ram:IssueDateTime', null, [
			'udt:DateTimeString' => [
				'_value' => date('Ymd', strtotime($invoice->invoice_date_created ?? 'now')),
				'_attributes' => ['format' => '102']
			]
		]));
		return $document;
	}

	private function createTradeTransaction($items) {
		$transaction = $this->doc->createElement('ram:SupplyChainTradeTransaction');

		foreach ($items as $item) {
			$line = $this->doc->createElement('ram:IncludedSupplyChainTradeLineItem');

			$product = $this->doc->createElement('ram:SpecifiedTradeProduct');
			$product->appendChild($this->createElementWithValue('ram:Name', htmlspecialchars($item->item_name)));
			if (!empty($item->item_description)) {
				$product->appendChild($this->createElementWithValue('ram:Description', htmlspecialchars($item->item_description)));
			}
			$line->appendChild($product);

			$agreement = $this->doc->createElement('ram:SpecifiedSupplyChainTradeAgreement');
			$price = $this->doc->createElement('ram:GrossPriceProductTradePrice');
			$price->appendChild($this->createElementWithValue('ram:ChargeAmount', number_format($item->item_price, 2, '.', '')));
			$agreement->appendChild($price);
			$line->appendChild($agreement);

			$delivery = $this->doc->createElement('ram:SpecifiedSupplyChainTradeDelivery');
			$delivery->appendChild($this->createElementWithValue('ram:BilledQuantity', number_format($item->item_quantity, 2, '.', ''), ['unitCode' => 'C62']));
			$line->appendChild($delivery);

			$settlement = $this->doc->createElement('ram:SpecifiedSupplyChainTradeSettlement');
			$lineAmount = number_format($item->item_subtotal, 2, '.', '');
			$settlement->appendChild($this->createElementWithValue('ram:LineTotalAmount', $lineAmount));
			$line->appendChild($settlement);

			$transaction->appendChild($line);
		}

		return $transaction;
	}

	private function createSettlement($invoice) {
		$settlement = $this->doc->createElement('ram:ApplicableHeaderTradeSettlement');
		$settlement->appendChild($this->createElementWithValue('ram:InvoiceCurrencyCode', 'EUR'));

		$payment = $this->doc->createElement('ram:SpecifiedTradeSettlementPaymentMeans');
		$payment->appendChild($this->createElementWithValue('ram:TypeCode', '42'));

		$institution = $this->doc->createElement('ram:PayeeSpecifiedCreditorFinancialInstitution');
		$institution->appendChild($this->createElementWithValue('ram:Name', 'N26 Bank'));
		$payment->appendChild($institution);

		$account = $this->doc->createElement('ram:PayeeSpecifiedCreditorFinancialAccount');
		$account->appendChild($this->createElementWithValue('ram:IBANID', $invoice->user_iban));
		$account->appendChild($this->createElementWithValue('ram:BICID', $invoice->user_subscribernumber));
		$payment->appendChild($account);

		$settlement->appendChild($payment);

		if (!empty($invoice->due_date)) {
			$terms = $this->doc->createElement('ram:SpecifiedTradePaymentTerms');
			$terms->appendChild($this->createElementWithValue('ram:DueDateDateTime', null, [
				'udt:DateTimeString' => [
					'_value' => date('Ymd', strtotime($invoice->due_date)),
					'_attributes' => ['format' => '102']
				]
			]));
			$settlement->appendChild($terms);
		}

		$tax = $this->doc->createElement('ram:ApplicableTradeTax');
		$tax->appendChild($this->createElementWithValue('ram:TypeCode', 'VAT'));
		$tax->appendChild($this->createElementWithValue('ram:BasisAmount', number_format($invoice->invoice_item_subtotal, 2, '.', '')));
		$tax->appendChild($this->createElementWithValue('ram:CalculatedAmount', number_format($invoice->invoice_item_tax_total, 2, '.', '')));
		$tax->appendChild($this->createElementWithValue('ram:CategoryCode', 'S'));
		$tax->appendChild($this->createElementWithValue('ram:RateApplicablePercent', '19.00'));
		$settlement->appendChild($tax);

		$summary = $this->doc->createElement('ram:SpecifiedTradeSettlementHeaderMonetarySummation');
		$summary->appendChild($this->createElementWithValue('ram:LineTotalAmount', number_format($invoice->invoice_item_subtotal, 2, '.', '')));
		$summary->appendChild($this->createElementWithValue('ram:TaxBasisTotalAmount', number_format($invoice->invoice_item_subtotal, 2, '.', '')));
		$summary->appendChild($this->createElementWithValue('ram:TaxTotalAmount', number_format($invoice->invoice_item_tax_total, 2, '.', '')));
		$summary->appendChild($this->createElementWithValue('ram:GrandTotalAmount', number_format($invoice->invoice_total, 2, '.', '')));
		$settlement->appendChild($summary);

		return $settlement;
	}

	private function createParty($tag, $name, $street, $zip, $city, $country, $vatId = null)
	{
		$party = $this->doc->createElement($tag);
		$party->appendChild($this->createElementWithValue('ram:Name', htmlspecialchars($name)));

		$address = $this->doc->createElement('ram:PostalTradeAddress');
		$address->appendChild($this->createElementWithValue('ram:PostcodeCode', htmlspecialchars($zip)));
		$address->appendChild($this->createElementWithValue('ram:LineOne', htmlspecialchars($street)));
		$address->appendChild($this->createElementWithValue('ram:CityName', htmlspecialchars($city)));
		$address->appendChild($this->createElementWithValue('ram:CountryID', htmlspecialchars($country)));

		$party->appendChild($address);

		if ($vatId) {
			$tax = $this->doc->createElement('ram:SpecifiedTaxRegistration');
			$tax->appendChild($this->createElementWithValue('ram:ID', $vatId));
			$tax->appendChild($this->createElementWithValue('ram:TypeCode', 'VA'));
			$party->appendChild($tax);
		}

		return $party;
	}

	private function createElementWithValue($name, $value = null, $nested = [])
	{
		$element = $this->doc->createElement($name);

		if ($value !== null) {
			$element->nodeValue = $value;
		}

		if (!empty($nested)) {
			foreach ($nested as $subName => $subValue) {
				if (is_array($subValue)) {
					$subEl = $this->doc->createElement($subName);
					foreach ($subValue['_attributes'] as $k => $v) {
						$subEl->setAttribute($k, $v);
					}
					$subEl->nodeValue = $subValue['_value'];
					$element->appendChild($subEl);
				} else {
					$element->appendChild($this->doc->createElement($subName, $subValue));
				}
			}
		}

		return $element;
	}
}