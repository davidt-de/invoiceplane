<?php

class ZugferdGenerator
{
	protected $doc;

	public function generate($invoice, $items)
	{
		$this->doc = new DOMDocument('1.0', 'UTF-8');
		$this->doc->formatOutput = true;

		$rsm = $this->doc->createElement('rsm:CrossIndustryInvoice');
		$rsm->setAttribute('xmlns:rsm', 'urn:ferd:CrossIndustryDocument:invoice:1p0:basic');
		$rsm->setAttribute('xmlns:ram', 'urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100');
		$rsm->setAttribute('xmlns:udt', 'urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100');

		// Header
		$rsm->appendChild($this->createElementWithValue('ram:ID', htmlspecialchars($invoice->invoice_number)));
		$rsm->appendChild($this->createElementWithValue('ram:Name', 'RECHNUNG'));

		// Seller Party
		$seller = $this->doc->createElement('ram:SellerTradeParty');
		$seller->appendChild($this->createElementWithValue('ram:Name', htmlspecialchars($invoice->user_name)));
		$sellerAddr = $this->doc->createElement('ram:PostalTradeAddress');
		$sellerAddr->appendChild($this->createElementWithValue('ram:PostcodeCode', htmlspecialchars($invoice->user_zip)));
		$sellerAddr->appendChild($this->createElementWithValue('ram:LineOne', htmlspecialchars($invoice->user_address_1)));
		$sellerAddr->appendChild($this->createElementWithValue('ram:CityName', htmlspecialchars($invoice->user_city)));
		$sellerAddr->appendChild($this->createElementWithValue('ram:CountryID', htmlspecialchars($invoice->user_country)));
		$seller->appendChild($sellerAddr);
		$rsm->appendChild($seller);

		// Buyer Party
		$buyer = $this->doc->createElement('ram:BuyerTradeParty');
		$buyer->appendChild($this->createElementWithValue('ram:Name', htmlspecialchars($invoice->client_name)));
		$buyerAddr = $this->doc->createElement('ram:PostalTradeAddress');
		$buyerAddr->appendChild($this->createElementWithValue('ram:PostcodeCode', htmlspecialchars($invoice->client_zip)));
		$buyerAddr->appendChild($this->createElementWithValue('ram:LineOne', htmlspecialchars($invoice->client_address_1)));
		$buyerAddr->appendChild($this->createElementWithValue('ram:CityName', htmlspecialchars($invoice->client_city)));
		$buyerAddr->appendChild($this->createElementWithValue('ram:CountryID', htmlspecialchars($invoice->client_country)));
		$buyer->appendChild($buyerAddr);
		$rsm->appendChild($buyer);

		// Invoice lines
		foreach ($items as $item) {
			$line = $this->doc->createElement('ram:IncludedSupplyChainTradeLineItem');
			$line->appendChild($this->createElementWithValue('ram:ID', htmlspecialchars($item->item_name)));
			$line->appendChild($this->createElementWithValue('ram:SpecifiedTradeProduct', htmlspecialchars($item->item_description)));
			$rsm->appendChild($line);
		}

		// Monetary summation
		$summation = $this->doc->createElement('ram:SpecifiedTradeSettlementHeaderMonetarySummation');
		$summation->appendChild($this->createElementWithValue('ram:LineTotalAmount', number_format($invoice->invoice_item_subtotal, 2, '.', '')));
		$summation->appendChild($this->createElementWithValue('ram:TaxTotalAmount', number_format($invoice->invoice_item_tax_total, 2, '.', '')));
		$summation->appendChild($this->createElementWithValue('ram:GrandTotalAmount', number_format($invoice->invoice_total, 2, '.', '')));
		$rsm->appendChild($summation);

		$this->doc->appendChild($rsm);

		return $this->doc->saveXML();
	}

	protected function createElementWithValue($name, $value)
	{
		$element = $this->doc->createElement($name);
		$element->appendChild($this->doc->createTextNode($value));
		return $element;
	}
} 
