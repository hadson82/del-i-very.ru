/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com> 
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
 */

$.formUtils.addValidator({name:"ukvatnumber",validatorFunction:function(number){number=number.replace(/[^0-9]/g,"");if(number.length<9){return false}var valid=false;var VATsplit=[];VATsplit=number.split("");var checkDigits=Number(VATsplit[7]+VATsplit[8]);var firstDigit=VATsplit[0];var secondDigit=VATsplit[1];if(firstDigit==0&&secondDigit>0){return false}var total=0;for(var i=0;i<7;i++){total+=VATsplit[i]*(8-i)}var c=0;var i=0;for(var m=8;m>=2;m--){c+=VATsplit[i]*m;i++}while(total>0){total-=97}total=Math.abs(total);if(checkDigits==total){valid=true}if(!valid){total=total%97;if(total>=55){total=total-55}else{total=total+42}if(total==checkDigits){valid=true}}return valid},errorMessage:"",errorMessageKey:"badUKVatAnswer"});