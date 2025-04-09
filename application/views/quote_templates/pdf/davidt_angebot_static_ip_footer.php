<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('quote'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css?cachebust=1324234">

</head>

<body>

<div id="pm-1"><hr /></div>
<div id="pm-2"><hr /></div>
<div id="pm-3"><hr /></div>

<div id="client">
  <div id="sichtfenster-absender">
    <?php 
       echo htmlsc($quote->user_company) . ' - ' . htmlsc($quote->user_address_1) . ' - ' . htmlsc($quote->user_zip) . ' ' . htmlsc($quote->user_city); 
    ?>
    <hr />
  </div>
  <div>
    <?php
     if (!empty($custom_fields['client']['Firma'])) {
          echo '<b>' . $custom_fields['client']['Firma'] . ' </b><br>' . lang($quote->client_title) . ' ' . $quote->client_name  . ' ' . $quote->client_surname . '<br>';
      } else {
          echo '<b>' . lang($quote->client_title) . ' ' . $quote->client_name  . ' ' . $quote->client_surname . ' </b>';
      }
      ?>

  </div>
  <?php if ($quote->client_vat_id) {
    echo '<div>' . trans('vat_id_short') . ': ' . $quote->client_vat_id . '</div>';
  }
  if ($quote->client_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . $quote->client_tax_code . '</div>';
  }
  if ($quote->client_address_1) {
    echo '<div>' . htmlsc($quote->client_address_1) . '</div>';
  }
  if ($quote->client_address_2) {
    echo '<div>' . htmlsc($quote->client_address_2) . '</div>';
  }
  if ($quote->client_city || $quote->client_state || $quote->client_zip) {
    echo '<div>';
        if ($quote->client_zip) {
      echo htmlsc($quote->client_zip) . ' ';
    }
    if ($quote->client_city) {
      echo htmlsc($quote->client_city) . ' ';
    }
    if ($quote->client_state) {
      echo htmlsc($quote->client_state);
    }
    echo '</div>';
  }
  if ($quote->client_country) {
    echo '<div>' . get_country_name(trans('cldr'), $quote->client_country) . '</div>';
  }

  echo '<br/>';

  if ($quote->client_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->client_phone) . '</div>';
  } ?>
</div>

<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

</header>

<main>

   <div class="invoice-details clearfix" style="height: 70px;">

    <span >&nbsp;</span>
   
 </div>

    <h1 class="invoice-title"><?php echo trans('quote') . ' ' . $quote->quote_number; ?></h1>
    
    <p class="intro">
     <?php
 
      echo $custom_fields['client']['Anrede'] .  ' ' . $quote->client_name  . ' ' . $quote->client_surname . ',<br>';
      ?>
     <br  />
     anbei übersende ich Ihnen das besprochene Angebot.
    </p>
    
<table style="width: 100%; font-size: 9pt; margin-bottom: 20px;">
  <tr valign="top">
    <td width="25%">
      <strong>Betreff</strong><br />
      <?php
        if (!empty($quote->notes)) {
            echo htmlsc($quote->notes);
        } else {
            echo 'Hosting ' . htmlsc($quote->client_web);
        }
      ?>
    </td>
      
      <td width="auto"><strong>Angebotsnummer</strong><br  />
        <?php echo htmlsc($quote->quote_number); ?></td>
     
     
        <td width="auto"><strong>Kundennummer</strong><br  />
          <?php echo str_pad($quote->client_id, 5, '0', STR_PAD_LEFT); ?></td>
          
          <td width="auto"> <strong>Gültig bis</strong><br  />
          <?php echo date_from_mysql($quote->quote_date_expires, true); ?></td>

        <td width="auto" align="right"><strong>Datum</strong><br  />
        <?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
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
                  <?php echo number_format($item->item_quantity, 2, ',', ''); ?>
                    <?php if ($item->item_product_unit) : ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                  <?php echo number_format($item->item_price, 2, ',', '') . '&nbsp;€'; ?>    
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
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

        <?php if ($quote->quote_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo 'zzgl. 19% USt.'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php echo 'zzgl. ' . htmlsc($quote_tax_rate->quote_tax_rate_name) . ' ' . number_format($quote_tax_rate->quote_tax_rate_percent, 0, ',', '') . '%'; ?>; 
                 
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($quote->quote_discount_percent != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($quote->quote_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($quote->quote_discount_amount != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($quote->quote_total); ?></b>
            </td>
        </tr>
        </tbody>
    </table>
    
<p class="outro">
     Dieses Angebot ist gültig bis zum  <?php echo date_from_mysql($quote->quote_date_expires, true); ?>. Sollten Sie Fragen oder Änderungswünsche haben, melden Sie sich gerne – ich bin jederzeit für Sie da.
     </p>

</main>
<footer>
  <!-- HTML-Code direkt in Systemeinstellungen → Rechnungen → PDF-Fußzeile  -->
</footer>
</body>

</html>
