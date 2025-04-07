<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('invoice'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
    <style>
/* general font size */
body {
  font-size: 12px;
}

/* color of Invoice Heading fixed */
.invoice-title {
    color: #000000;
}

/* left space due to DIN 5008 2.5mm */
main, footer {
    margin-left: 2.5mm;
}

/* reduces padding in item-table */
table.item-table td {
    padding: 5px 10px;
}

/* defines german DIN_5008_Form_B sichtbriefumschlag position */
#client {
    position: absolute; 
    top: 56mm;
    left: 20mm;
    width: 90mm;
}

/* reduced font-size for items */
table.item-table {
  font-size: 11px !important;
}

/* sender in sichtfenster */
#sichtfenster-absender {
  font-size: 8px;
}

/** Pfalzmarken DIN 5008 Form B */
/* 105mm from top with offset */
#pm-1{
    position: absolute;
    top: 101.8mm;
    left: 5mm;
    width: 5mm;
}
/* 148.5mm from top with offset */
#pm-2{
    position: absolute;
    top: 145.3mm;
    left: 5mm;
    width: 7mm;
}
/* 210mm from top with offset */
#pm-3{
    position: absolute;
    top: 206.8mm;
    left: 5mm;
    width: 5mm;
}
/* Deaktiviert den Zebra-Style der Tabelle */
table.item-table tr:nth-child(2n-1) td {
  background: transparent !important;
}
table.item-table thead {
  background-color: #f0f0f0;
}
#logo {
  float: left;
  text-align: left !important;
  margin-bottom: 20px;
}
footer {
  border-top: 1px solid #878686;
}
    </style>
</head>

<body>

<div id="pm-1"><hr /></div>
<div id="pm-2"><hr /></div>
<div id="pm-3"><hr /></div>

<div id="client">
	<div id="sichtfenster-absender">
		<?php 
		   echo htmlsc($invoice->user_company) . ' - ' . htmlsc($invoice->user_address_1) . ' - ' . htmlsc($invoice->user_zip) . ' ' . htmlsc($invoice->user_city); 
		?>
		<hr />
	</div>
	<div>
		<b><?php _htmlsc(format_client($invoice)); ?></b>
	</div>
	<?php if ($invoice->client_vat_id) {
		echo '<div>' . trans('vat_id_short') . ': ' . $invoice->client_vat_id . '</div>';
	}
	if ($invoice->client_tax_code) {
		echo '<div>' . trans('tax_code_short') . ': ' . $invoice->client_tax_code . '</div>';
	}
	if ($invoice->client_address_1) {
		echo '<div>' . htmlsc($invoice->client_address_1) . '</div>';
	}
	if ($invoice->client_address_2) {
		echo '<div>' . htmlsc($invoice->client_address_2) . '</div>';
	}
	if ($invoice->client_city || $invoice->client_state || $invoice->client_zip) {
		echo '<div>';
        if ($invoice->client_zip) {
			echo htmlsc($invoice->client_zip) . ' ';
		}
		if ($invoice->client_city) {
			echo htmlsc($invoice->client_city) . ' ';
		}
		if ($invoice->client_state) {
			echo htmlsc($invoice->client_state);
		}
		echo '</div>';
	}
	if ($invoice->client_country) {
		echo '<div>' . get_country_name(trans('cldr'), $invoice->client_country) . '</div>';
	}

	echo '<br/>';

	if ($invoice->client_phone) {
		echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($invoice->client_phone) . '</div>';
	} ?>
</div>

<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

	<!-- client is extracted due to absolute position -->

   
</header>

<main style="min-height: 22cm;">

    <div class="invoice-details clearfix">
       <?php if (get_setting('qr_code')) { ?>
           <table class="invoice-qr-code-table">
               <tr>
                   
                   <td class="text-right" style="font-size: 7pt">
                       <?php echo invoice_qrcode(htmlsc($invoice->invoice_id)); ?>
                       <br> Bezahlen mit Giro-QR-Code
                     </td>
               </tr>
           </table>
       <?php } ?>
    </div>

    <h1 class="invoice-title"><?php echo trans('invoice') . ' ' . $invoice->invoice_number; ?></h1>
<table style="width: 100%; font-size: 9pt; margin-bottom: 20px;">
  <tr valign="top">
    <td width="25%">
     <strong>Betreff</strong><br  />
      Hosting für <?php echo htmlsc($invoice->client_name); ?></td>
      
      <td width="auto"><strong>Rechnungsnummer</strong><br  />
        <?php echo htmlsc($invoice->invoice_number); ?></td>
     
     
        <td width="auto"><strong>Kundennummer</strong><br  />
          <?php echo str_pad($invoice->client_id, 5, '0', STR_PAD_LEFT); ?></td>
          
          <td width="auto"> <strong>Leistungszeitraum</strong><br  />
          <?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>

        <td width="auto" align="right"><strong>Datum</strong><br  />
        <?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
      </tr>
    </table>
    <table class="item-table">
        <thead>        
          <tr>
           <th style="background-color: #f0f0f0;" class="item-name"><?php _trans('item'); ?></th>
           <th style="background-color: #f0f0f0;" class="item-desc"><?php _trans('description'); ?></th>
            <th style="background-color: #f0f0f0;" class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th style="background-color: #f0f0f0;" class="item-price text-right"><?php _trans('price'); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th style="background-color: #f0f0f0;" class="item-discount text-right"><?php _trans('discount'); ?></th>
            <?php endif; ?>
            <th style="background-color: #f0f0f0;" class="item-total text-right"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                    <?php if ($item->item_product_unit) : ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
                <?php if ($show_item_discounts) : ?>
                    <td class="text-right">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right">
                    <?php echo format_currency($item->item_total); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
        </tr>

        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo 'zzgl. 19% USt.'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($invoice->invoice_discount_percent != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($invoice->invoice_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($invoice->invoice_discount_amount != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php _trans('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($invoice->invoice_paid); ?>
            </td>
        </tr>
        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('balance'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_balance); ?></b>
            </td>
        </tr>
        </tbody>
    </table>
    
<div style="margin-top: 20px; font-size: 9pt; line-height: 1.4;">
      <strong>Zahlungsbedingungen:</strong><br>
      Bitte begleichen Sie den offenen Betrag bis zum <?php echo date_from_mysql($invoice->invoice_date_due, true); ?>.<br>
      Die Zahlung ist möglich per:<br>
      • SEPA-Überweisung (bequem mit dem GiroCode oben rechts)<br>
      • Lastschrift oder Kreditkarte: <a href="<?php echo site_url('guest/view/invoice/' . $invoice->invoice_url_key); ?>">Jetzt online bezahlen</a>
    </div>

</main>


<!-- <div style="height: 200px;"></div> -->
<?php if (count($items) < 10): ?>
  <div style="height: 100px;"></div>
<?php endif; ?>


<footer style="font-size: 8pt; margin-top: 30px;line-height: 1.3;">

  
  <table width="100%">
    <tr valign="top">
      <td valign="top" width="25%">
       <b><?php _htmlsc($invoice->user_company); ?></b><br>
       <?php if ($invoice->user_address_1) {
           echo  htmlsc($invoice->user_address_1) . '<br>';
       }
       if ($invoice->user_address_2) {
           echo htmlsc($invoice->user_address_2) . '<br>'; 
       }
       if ($invoice->user_city || $invoice->user_state || $invoice->user_zip) {
          
           if ($invoice->user_zip) {
               echo htmlsc($invoice->user_zip) . ' ';
           }
           if ($invoice->user_city) {
               echo htmlsc($invoice->user_city) . ' ';
           }
           if ($invoice->user_state) {
               echo htmlsc($invoice->user_state);
           }
           echo '<br>';
       }
        if ($invoice->user_vat_id) {
            echo 'USt-ID: ' . $invoice->user_vat_id . '<br>';} 
       
       if ($invoice->user_name) {
       echo 'Inhaber: ' . $invoice->user_name . '<br>';} 
       
       ?>
      </td>
      <td valign="top" width="25%">
        <strong>Kontakt:</strong><br>
        Tel: 0172 2063265<br>
        E-Mail: patrick@davidt.de<br>
        Web: davidt.de
      </td>
      <td  valign="top" width="25%" >
        <strong>Bankverbindung:</strong><br  />
        Bank: N26 Bank<br>
        IBAN: <?php if ($invoice->user_iban) {
        echo $invoice->user_iban; } ?><br>
        BIC: <?php if ($invoice->user_subscribernumber) {
        echo $invoice->user_subscribernumber; } ?>
       
      </td>
      
    </tr>
  </table>
</footer>
</body>

</html>
