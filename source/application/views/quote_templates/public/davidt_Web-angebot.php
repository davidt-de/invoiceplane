<!DOCTYPE html>
<html lang="<?php echo trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>
        <?php echo get_setting('custom_title', 'InvoicePlane', true); ?>
        - <?php echo trans('invoice'); ?> <?php echo $quote->invoice_number; ?>
    </title>

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom.css?cache=65487">
    
</head>
<body>

<div class="container">
    <div id="content">

       <div class="webpreview-header">
       
           <h2><?php echo trans('quote') . ' ' . $quote->quote_number; ?></h2>
       
           <div class="btn-group">
               <?php if (in_array($quote->quote_status_id, array(2, 3))) : ?>
                   <a href="<?php echo site_url('guest/view/approve_quote/' . $quote_url_key); ?>"
                      class="btn btn-success">
                       <i class="fa fa-check"></i><?php echo trans('approve_this_quote'); ?>
                   </a>
                   <a href="<?php echo site_url('guest/view/reject_quote/' . $quote_url_key); ?>"
                      class="btn btn-danger">
                       <i class="fa fa-times-circle"></i><?php echo trans('reject_this_quote'); ?>
                   </a>
               <?php endif; ?>
               <a href="<?php echo site_url('guest/view/generate_quote_pdf/' . $quote_url_key); ?>"
                  class="btn btn-primary">
                   <i class="fa fa-print"></i> <?php echo trans('download_pdf'); ?>
               </a>
           </div>
       
       </div>

        <hr>

        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <div class="invoice">

            <?php
            $logo = invoice_logo();
            if ($logo) {
                echo $logo . '<br><br>';
            }
            ?>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-6">

                    <h4><?php _htmlsc($quote->user_company); ?></h4>
                    <p>
                        <?php if ($quote->user_address_1) {
                            echo htmlsc($quote->user_address_1) . '<br>';
                        } ?>
                        <?php if ($quote->user_address_2) {
                            echo htmlsc($quote->user_address_2) . '<br>';
                        } ?>
                        <?php if ($quote->user_zip) {
                        echo htmlsc($quote->user_zip) . ' ';
                        } ?>
                       
                        <?php if ($quote->user_city) {
                            echo htmlsc($quote->user_city) . '<br>';
                        } ?>
                       
                       
                       
                        
                        <?php if ($quote->user_mobile) { ?><?php echo trans('phone_abbr'); ?>: <?php echo htmlsc($quote->user_mobile); ?>
                            <br><?php } ?>
                        <?php if ($quote->user_fax) { ?><?php echo trans('fax_abbr'); ?>: <?php echo htmlsc($quote->user_fax); ?><?php } ?>
                        <?php if ($quote->user_vat_id) {
                            echo lang("vat_id_short") . ": " . $quote->user_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->user_tax_code) {
                            echo lang("tax_code_short") . ": " . $quote->user_tax_code . '<br>';
                        } ?>
                    </p>

                </div>
               
                
                <div class="col-xs-12 col-md-6 col-lg-6 text-right">
                     <?php
                       if (!empty($custom_fields['client']['Firma'])) {
                            echo '<h4>' . $custom_fields['client']['Firma'] . ' </h4>' . lang($quote->client_title) . ' ' . $quote->client_name  . ' ' . $quote->client_surname . '<br>';
                        } else {
                            echo '<h4>' . lang($quote->client_title) . ' ' . $quote->client_name  . ' ' . $quote->client_surname . ' </h4>';
                        }
                        ?>
                    
                   
                    
                    
                    <p><?php if ($quote->client_vat_id) {
                            echo lang("vat_id_short") . ": " . $quote->client_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->client_tax_code) {
                            echo lang("tax_code_short") . ": " . $quote->client_tax_code . '<br>';
                        } ?>
                        <?php if ($quote->client_address_1) {
                            echo htmlsc($quote->client_address_1) . '<br>';
                        } ?>
                        <?php if ($quote->client_address_2) {
                            echo htmlsc($quote->client_address_2) . '<br>';
                        } ?>
                        <?php if ($quote->client_zip) {
                            echo htmlsc($quote->client_zip) . ' ';
                        } ?>
                        <?php if ($quote->client_city) {
                            echo htmlsc($quote->client_city) . '<br>';
                        } ?>
                        <?php if ($quote->client_phone) {
                            echo trans('phone_abbr') . ': ' . htmlsc($quote->client_phone); ?>
                            <br>
                        <?php } ?>
                    </p>

                    <br>

                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td><?php echo trans('quote_date'); ?></td>
                            <td style="text-align:right;"><?php echo date_from_mysql($quote->quote_date_created); ?></td>
                        </tr>
                        <tr class="<?php echo($is_expired ? 'overdue' : '') ?>">
                            <td><?php echo trans('expires'); ?></td>
                            <td class="text-right">
                                <?php echo date_from_mysql($quote->quote_date_expires); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo trans('total'); ?></td>
                            <td class="text-right"><?php echo format_currency($quote->quote_total); ?></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <br>
            <div class="table-responsive">
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
                     <?php echo date_from_mysql($quote->quote_date_expires); ?>
            
                    <td width="auto" align="right"><strong>Datum</strong><br  />
                    <?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
                  </tr>
                </table>
            </div>
            <div class="invoice-items">
                <div class="table-responsive">
                    <table class="table ">
                        <thead style="background-color: #f0f0f0">
                        <tr>
                            <th><?php echo trans('item'); ?></th>
                            <th><?php echo trans('description'); ?></th>
                            <th class="text-right"><?php echo trans('qty'); ?></th>
                            <th class="text-right"><?php echo trans('price'); ?></th>
                            <th class="text-right"><?php echo trans('discount'); ?></th>
                            <th class="text-right"><?php echo trans('total'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php _htmlsc($item->item_name); ?></td>
                                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                <td class="amount">
                                    <?php echo format_quantity($item->item_quantity); ?>
                                    <?php if ($item->item_product_unit) : ?>
                                        <br>
                                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="amount">
                                <?php echo format_currency($item->item_price); ?> </td>
                                <td class="amount"><?php echo format_currency($item->item_discount); ?></td>
                                <td class="amount"><?php echo format_currency($item->item_total); ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr>
                            <td colspan="4"></td>
                            <td class="text-right"><?php echo trans('subtotal'); ?>:</td>
                            <td class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
                        </tr>

                        <?php if ($quote->quote_item_tax_total > 0) { ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right"><?php echo 'zzgl. 19% USt.'; ?></td>
                                <td class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right">
                                    <?php echo 'Zzgl. ' . htmlsc($quote_tax_rate->quote_tax_rate_name) . ' ' . number_format($quote_tax_rate->quote_tax_rate_percent, 0, ',', ''); 
                                     ?>
                                    %
                                </td>
                                <td class="amount"><?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?></td>
                            </tr>
                        <?php endforeach ?>

                        <?php if ($quote->quote_discount_percent > 0 || $quote->quote_discount_amount > 0) : ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right"><?php echo trans('discount'); ?>:</td>
                                <td class="amount">
                                    <?php
                                    if ($quote->quote_discount_percent > 0) {
                                        echo format_amount($quote->quote_discount_percent) . ' %';
                                    } else {
                                        echo format_amount($quote->quote_discount_amount);
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('total'); ?>:</td>
                            <td class="amount"><?php echo format_currency($quote->quote_total); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

               

            </div><!-- .invoice-items -->

            <hr>



            <div class="row">

                
                <?php
                if (count($attachments) > 0) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo trans('attachments'); ?></h4>
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <?php foreach ($attachments as $attachment) { ?>
                                    <tr class="attachments">
                                        <td><?php echo $attachment['name']; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('guest/get/attachment/' . $attachment['fullname']); ?>"
                                               class="btn btn-primary btn-sm">
                                                <i class="fa fa-download"></i> <?php echo trans('download') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div><!-- .invoice-items -->
    </div><!-- #content -->
</div>

</body>
</html>
