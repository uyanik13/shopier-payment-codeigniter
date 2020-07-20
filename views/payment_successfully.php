<section class="add-funds m-t-50">   
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-5">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="m-t-10"><i class="fe fe-check-circle text-primary"></i> <?=lang("payment_sucessfully")?></h3>
          </div>
          <div class="card-body">
            <?php if(!empty($transaction) && $transaction->type == 'paypal'){?>
            <div class="for-group text-center">
	            <img src="<?=BASE?>/assets/images/paypal.svg" alt="Paypay icon">
          	</div>
            <?php }?> 

            <?php if(!empty($transaction) && $transaction->type == '2checkout'){?>
            <div class="for-group text-center">
              <img src="<?=BASE?>/assets/images/2checkout.svg" alt="2checkout icon">
            </div>
            <?php }?>

          	<div class="detail">
	            <p class="p-t-10"><?=lang("your_payment_has_been_processed_here_are_the_details_of_this_transaction_for_your_reference")?></p>
	            <ul>
	            	<li><?=lang("Transaction_ID")?>: <strong><?=(!empty($transaction) && $transaction->transaction_id == 'empty') ? lang($transaction->transaction_id)." ".lang("transaction_id_was_sent_to_your_email") : $transaction->transaction_id?></strong></li>
	            	<li><?=lang("Amount_paid_includes_fee")?>: <strong><?=(!empty($transaction)) ? $transaction->amount : ''?> <?=get_option("currency_code", "USD")?></strong> </li>
	            </ul>
          	</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

