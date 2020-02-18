{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script text="javascript">
{literal}
$('document').ready(function(){
	$('#send_friend_button').fancybox({
		autoScale : true,
		'hideOnContentClick': false,
		'hideOnOverlayClick': true
	});

	$('#sendEmail').click(function(){

        var name = $('#friend_name').val();
        var email = $('#friend_email').val();
        var id_product = $('#id_product_comment_send').val();
		if (name && email && !isNaN(id_product))
		{
			$.ajax({
				{/literal}url: "{$module_dir}sendtoafriend_ajax.php",{literal}
				type: "POST",
				headers: {"cache-control": "no-cache"},
				data: {action: 'sendToMyFriend', secure_key: '{/literal}{$stf_secure_key}{literal}', name: name, email: email, id_product: id_product},{/literal}{literal}
				dataType: "json",
				success: function(result) {
					$.fancybox.close();
                    var msg = result ? "{/literal}{l s='Your e-mail has been sent successfully' mod='sendtoafriend'}{literal}" : "{/literal}{l s='Your e-mail could not be sent. Please check the e-mail address and try again.' mod='sendtoafriend'}{literal}";
                    var title = "{/literal}{l s='Send to a friend' mod='sendtoafriend'}{literal}";
                    fancyMsgBox(msg, title);
				}
			});
		}
		else
			$('#send_friend_form_error').text("{/literal}{l s='You did not fill required fields' mod='sendtoafriend' js=1}{literal}");
	});
});
{/literal}
</script>
<li class="sendtofriend">
	<a id="send_friend_button" href="#send_friend_form"><i class="icon-envelope"></i>{l s='Send to a friend' mod='sendtoafriend'}</a>
</li>

<div style="display: none;">
	<div id="send_friend_form">
    	<div id="send_friend_form_content">
			<h2 class="title clearfix">{l s='Send to a friend' mod='sendtoafriend'}</h2>
            <div class="row">
            <div class="col-xs-12 col-sm-6 titled_box">
            	<h2 class="product_name"><span>{$stf_product->name}</span></h2>
                <div class="product media clearfix">
                    <img class="img-thumbnail pull-left" src="{$link->getImageLink($stf_product->link_rewrite, $stf_product_cover, 'small_default')|escape:'html'}" alt="{$stf_product->name|escape:html:'UTF-8'}" />
                    <div class="product_desc">
                        {$stf_product->description_short}
                    </div>
                </div>
			</div>
            <div class="col-xs-12 col-sm-6">
			<div class="send_friend_form_content">
				<div id="send_friend_form_error" class="alert alert-danger" style="display:none;"></div>
				<div class="form_container titled_box">
					<h2 class="intro_form "><span>{l s='Recipient' mod='sendtoafriend'} :</span></h2>
					<p class="form-group">
						<label for="friend_name">{l s='Name of your friend' mod='sendtoafriend'} <sup class="required">*</sup> :</label>
						<input class="form-control" id="friend_name" name="friend_name" type="text" value=""/>
					</p>
					<p class="form-group">
						<label for="friend_email">{l s='E-mail address of your friend' mod='sendtoafriend'} <sup class="required">*</sup> :</label>
						<input class="form-control" id="friend_email" name="friend_email" type="email" value=""/>
					</p>
					<p class="txt_required"><sup class="required">*</sup> {l s='Required fields' mod='sendtoafriend'}</p>
				</div>
			</div>
            </div>
            </div>
            <p class="submit sendfrend_footer">
					<input id="id_product_comment_send" name="id_product" type="hidden" value="{$stf_product->id}" />
					<button class="btn btn-default" onclick="$.fancybox.close();">{l s='Cancel' mod='sendtoafriend'}</button>
					<button id="sendEmail" class="btn btn-default" name="sendEmail" type="submit" />{l s='Send' mod='sendtoafriend'}</button>
                    <!--input id="sendEmail" class="btn btn-inverse" name="sendEmail" type="submit" value="{l s='Send' mod='sendtoafriend'}" /-->
				</p>
	</div>
</div>
</div>
