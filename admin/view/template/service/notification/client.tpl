<?php echo $header; ?>
<script>
  var provider_info = <?php echo json_encode($provider_info); ?>;
  if(!provider_info){provider_info = {};}
  var provider_form = <?php echo json_encode($provider_form); ?>;
  var provider = '<?php echo $provider; ?>';
</script>

<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-notification" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-notification" class="form-horizontal">


          <div class="form-group">
            <label class="col-sm-2 control-label" ><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" ><?php echo $text_provider; ?></label>
            <div class="col-sm-10">
              <select name="provider" class="form-control">
                <option value="0"><?php echo $text_selects; ?></option>
                <?php foreach($providers as $_provider){ ?>
                  <option value="<?php echo $_provider; ?>" <?php echo($provider == $_provider ? 'selected' : ''); ?> ><?php echo $_provider; ?></option>
                <?php } ?>
              </select>
              <?php if($provider == 'whatsapp'){ ?>
                <br>
                <a href="<?php echo $open_whatsapp; ?>" target="_blank" class="btn btn-default"><?php echo $text_open_whatsapp; ?></a>
              <?php } ?>
            </div>
          </div>

          <div class="provider_form">
          </div>

          <div class="form-group flex f-column">

            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab-customer" data-toggle="tab"><?php echo $text_customer; ?></a></li>
              <li><a href="#tab-admin" data-toggle="tab"><?php echo $text_admin; ?></a></li>
            </ul>
						<div class="tab-content">
							<div class="tab-pane active flex" id="tab-customer">


                <div class="order_statuses flex f-column col-sm-2">
                  <div class="head"><?php echo $notify_about; ?></div>

                  <div class="order_status flex f-ai" data-key="new_order">
                    <input type="checkbox" name="new_order[status]" <?php echo (isset($new_order['status']) && $new_order['status'] ? 'checked' : ''); ?> value="1" >
                    <span class="checkbox"></span>
                    <span><?php echo $text_new_order; ?></span>
                  </div>

                  <?php foreach($order_statuses as $order_status){ ?>
                    <?php $key = "order_status_" . $order_status['order_status_id']; ?>
                    <div class="order_status flex f-ai" data-key="<?php echo $key; ?>">
                      <input type="checkbox" name="<?php echo $key; ?>[status]" <?php echo (isset(${$key}['status']) && ${$key}['status'] ? 'checked' : ''); ?> value="1" >
                      <span class="checkbox"></span>
                      <span><?php echo $order_status['name']; ?></span>
                    </div>
                  <?php } ?>

                  <div class="order_status flex f-ai" data-key="register_customer">
                    <input type="checkbox" name="register_customer[status]" <?php echo (isset($register_customer['status']) && $register_customer['status'] ? 'checked' : ''); ?> value="1" >
                    <span class="checkbox"></span>
                    <span><?php echo $text_register; ?></span>
                  </div>
                </div>

                <div class="order_status_forms col-sm-10">
                  <div class="order_status_form hidden" data-key="new_order">
                      <div class="head flex f-wrap">
                        <?php foreach($short_codes as $code => $name){ ?>
                          <a href="#" data-code="<?php echo $code; ?>" class="copy"><?php echo $name; ?></a>
                        <?php } ?>
                      </div>
                      <textarea name="new_order[message]" placeholder="<?php echo $text_new_order; ?>" class="form-control first"><?php echo (isset($new_order['message']) ? $new_order['message'] : ''); ?></textarea>
                      <textarea readonly class="form-control last"></textarea>
                  </div>


                  <?php foreach($order_statuses as $order_status){ ?>
                    <?php $key = "order_status_" . $order_status['order_status_id']; ?>
                    <div class="order_status_form hidden" data-key="<?php echo $key; ?>">
                      <div class="head flex f-wrap">
                        <?php foreach($short_codes as $code => $name){ ?>
                          <a href="#" data-code="<?php echo $code; ?>" class="copy"><?php echo $name; ?></a>
                        <?php } ?>
                      </div>
                      <textarea name="<?php echo $key; ?>[message]" placeholder="<?php echo $order_status['name']; ?>" class="form-control first"><?php echo (isset(${$key}['message']) ? ${$key}['message'] : ''); ?></textarea>
                      <textarea readonly class="form-control last"></textarea>
                    </div>
                  <?php } ?>

                  <div class="order_status_form hidden" data-key="register_customer">
                    <div class="head flex f-wrap">
                      <?php foreach($short_codes_customer as $code => $name){ ?>
                        <a href="#" data-code="<?php echo $code; ?>" class="copy"><?php echo $name; ?></a>
                      <?php } ?>
                    </div>
                    <textarea name="register_customer[message]" placeholder="<?php echo $text_register; ?>" class="form-control first"><?php echo (isset($register_customer['message']) ? $register_customer['message'] : ''); ?></textarea>
                    <textarea readonly class="form-control last"></textarea>
                  </div>
                </div>

              </div>
              <div class="tab-pane flex" id="tab-admin">
                
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $text_admin_phones; ?></label>
                  <div class="col-sm-10">
                    <input name="admin_phones" class="form-control" value="<?php echo $admin_phones; ?>">
                  </div>
                </div>

                <div class="order_statuses flex f-column col-sm-2">
                  <div class="head"><?php echo $notify_about; ?></div>

                  <div class="order_status flex f-ai" data-key="admin_new_order">
                    <input type="checkbox" name="admin_new_order[status]" <?php echo (isset($admin_new_order['status']) && $admin_new_order['status'] ? 'checked' : ''); ?> value="1" >
                    <span class="checkbox"></span>
                    <span><?php echo $text_new_order; ?></span>
                  </div>

                  <?php foreach($order_statuses as $order_status){ ?>
                    <?php $key = "admin_order_status_" . $order_status['order_status_id']; ?>
                    <div class="order_status flex f-ai" data-key="<?php echo $key; ?>">
                      <input type="checkbox" name="<?php echo $key; ?>[status]" <?php echo (isset(${$key}['status']) && ${$key}['status'] ? 'checked' : ''); ?> value="1" >
                      <span class="checkbox"></span>
                      <span><?php echo $order_status['name']; ?></span>
                    </div>
                  <?php } ?>

                  <div class="order_status flex f-ai" data-key="admin_register_customer">
                    <input type="checkbox" name="admin_register_customer[status]" <?php echo (isset($admin_register_customer['status']) && $admin_register_customer['status'] ? 'checked' : ''); ?> value="1" >
                    <span class="checkbox"></span>
                    <span><?php echo $text_register; ?></span>
                  </div>
                </div>

                <div class="order_status_forms col-sm-10">
                  <div class="order_status_form hidden" data-key="admin_new_order">
                      <div class="head flex f-wrap">
                        <?php foreach($short_codes as $code => $name){ ?>
                          <a href="#" data-code="<?php echo $code; ?>" class="copy"><?php echo $name; ?></a>
                        <?php } ?>
                      </div>
                      <textarea name="admin_new_order[message]" placeholder="<?php echo $text_new_order; ?>" class="form-control first"><?php echo (isset($admin_new_order['message']) ? $admin_new_order['message'] : ''); ?></textarea>
                      <textarea readonly class="form-control last"></textarea>
                  </div>


                  <?php foreach($order_statuses as $order_status){ ?>
                    <?php $key = "admin_order_status_" . $order_status['order_status_id']; ?>
                    <div class="order_status_form hidden" data-key="<?php echo $key; ?>">
                      <div class="head flex f-wrap">
                        <?php foreach($short_codes as $code => $name){ ?>
                          <a href="#" data-code="<?php echo $code; ?>" class="copy"><?php echo $name; ?></a>
                        <?php } ?>
                      </div>
                      <textarea name="<?php echo $key; ?>[message]" placeholder="<?php echo $order_status['name']; ?>" class="form-control first"><?php echo (isset(${$key}['message']) ? ${$key}['message'] : ''); ?></textarea>
                      <textarea readonly class="form-control last"></textarea>
                    </div>
                  <?php } ?>

                  <div class="order_status_form hidden" data-key="admin_register_customer">
                    <div class="head flex f-wrap">
                      <?php foreach($short_codes_customer as $code => $name){ ?>
                        <a href="#" data-code="<?php echo $code; ?>" class="copy"><?php echo $name; ?></a>
                      <?php } ?>
                    </div>
                    <textarea name="admin_register_customer[message]" placeholder="<?php echo $text_register; ?>" class="form-control first"><?php echo (isset($admin_register_customer['message']) ? $admin_register_customer['message'] : ''); ?></textarea>
                    <textarea readonly class="form-control last"></textarea>
                  </div>
                </div>

              </div>
          </div>


        </form>
      </div>
    </div>
  </div>
</div>
<style>

.flex, .input.flex{
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
}
.f-column{
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
}
.f-ai{
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
}
.f-jc{
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
}
.pull-left{
	margin-right: auto !important;
}
.pull-right{
	margin-left: auto !important;
}
.head {
  font-size: 16px;
  font-weight: bold;
  padding: 10px;
}

.order_status {
  cursor: pointer;
  padding: 8px 10px;
  border: 1px solid gainsboro;
  border-radius: 5px;
  margin-left: 5px;
  margin-bottom: 5px;
  position: relative;
  background: #578fd5;
  color: #FFF;
  font-weight: bold;
}
.order_statuses{
    padding: 0;
}
.order_status input{
  display: none;
}
span.checkbox {
  display: inline-block;
  width: 20px;
  height: 20px;
  min-height: 20px !important;
  margin-right: 8px;
  border: 1px solid gainsboro;
  background: #FFF;
  padding: 0 !important;
}
.order_status input:checked + span:before {
  position: absolute;
  content: "\2714";
  color: #00e500;
  font-size: 20px;
  line-height: 20px;
}
.order_status.active {
    background: #1aa825;
}
.order_status_form textarea{
  min-height: 100px;
}
.order_status_form {
    padding: 20px;
    width: 100%;
}
.order_status_form textarea {
    margin-bottom: 20px;
}
.alert{
  position: fixed;
  top: 100px;
  right: 30px;
  max-width: 200px;
}
.order_status_form .head {
    padding: 10px 0;
}
.order_status_form .head a {
    margin-right: 10px;
    position: relative;
}
</style>
<script>
var form = {
  'cursor': null,
  'form': null,
  'bind': function($form){
    form.cursor = $form.selectionEnd;
    var str = $form.val();
    str = str.replace(/\{ID\}/g, '253');
    str = str.replace(/\{DATE\}/g, '20-01-2019');
    str = str.replace(/\{TIME\}/g, '16:33');
    str = str.replace(/\{SUM\}/g, '4356.6');
    str = str.replace(/\{NAME\}/g, '<?php echo $replace_default_name; ?>');
    str = str.replace(/\{STATUS\}/g, '<?php echo $replace_default_status; ?>');
    str = str.replace(/\{COMMENT\}/g, '<?php echo $replace_default_comment; ?>');
    str = str.replace(/\{PASSWORD\}/g, 'vasya052016');
    str = str.replace(/\{LOGIN\}/g, 'vasya052016@email.ru');
    $form.parent().find('.last').val(str);
  },
  'insert': function(text, offset) {
    if(!form.form){return;}
    el = form.form[0];
    
    var val = el.value, endIndex, range, doc = el.ownerDocument;
    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number"){
      endIndex = el.selectionEnd;
      el.value = val.slice(0, endIndex) + text + val.slice(endIndex);
      el.selectionStart = el.selectionEnd = endIndex + text.length+(offset?offset:0);
    } else if (doc.selection != "undefined" && doc.selection.createRange) {
      el.focus();
      range = doc.selection.createRange();
      range.collapse(false);
      range.text = text;
      range.select();
    }
    form.form.keyup();
    form.form.focus();
  }
}
$(document).on('click','.order_status .checkbox',function(){
  $input = $(this).parent().find('input');
  if($input.prop('checked')){
    $input.prop('checked', false);
  }else{
    $input.prop('checked', true);
  }
});

$(document).on('click','#tab-customer .order_status',function(){
  var key = $(this).data('key');
  $('#tab-customer .order_status').removeClass('active');
  $(this).addClass('active');
  $('#tab-customer .order_status_form').addClass('hidden');
  $('#tab-customer .order_status_form[data-key="'+key+'"]').removeClass('hidden');
  form.form = null;
});

$('#tab-customer .order_status').first().click();

$(document).on('click','#tab-admin .order_status',function(){
  var key = $(this).data('key');
  $('#tab-admin .order_status').removeClass('active');
  $(this).addClass('active');
  $('#tab-admin .order_status_form').addClass('hidden');
  $('#tab-admin .order_status_form[data-key="'+key+'"]').removeClass('hidden');
  form.form = null;
});

$('#tab-admin .order_status').first().click();

$('.order_status_form .first').on('keyup paste',function(){
	form.bind($(this));
});

$('.order_status_form .first').on('focus',function(){
	form.form = $(this);
});

$('.order_status_form .first').each(function(){
  $(this).keyup();
});

function copy($el) {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val($el.data('code')).select(); 
	document.execCommand("copy");
	$temp.remove();
}
function addAlertSuccess(text, timeLife){
	if(!timeLife){
		timeLife = 3000;
	}
	$('body').append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+text+' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
	setTimeout(function(){
		$('.alert').remove();
	}, timeLife);
}
$(document).on('click','.copy',function(e){
  e.preventDefault();
	copy($(this));
  addAlertSuccess('<?php echo $text_copied; ?>', 1000);
  form.insert($(this).data('code'));
});

$(document).on('change','[name="provider"]', function(){
  var provider_name = $(this).val();

  var html = '<div class="form-group"  style="padding:0"></div>';

  var info = provider_form['default'];
  if(provider_form[provider_name]){
    info = provider_form[provider_name];
  }

  $.each(info, function(key, val){
    if(val){
      var value = '';
      if(provider_info[key] && provider == provider_name){
        value = provider_info[key];
      }
      html += `
      <div class="form-group">
        <label class="col-sm-2 control-label">`+val+`</label>
        <div class="col-sm-10">
          <input name="provider_info[`+key+`]" class="form-control" value="`+value+`">
        </div>
      </div>`;
    }
  });

  html += '<div class="form-group" style="padding:0"></div>';
  $('.provider_form').html(html);
});

$('[name="provider"]').change();

</script>

<?php echo $footer; ?>