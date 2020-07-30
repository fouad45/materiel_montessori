{*
 * 2019 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 *}
 <div class="help-block" id="slq-touchmap-width">
    {l s='Format:' mod='touchize'}&nbsp;JPG, GIF, PNG.&nbsp;{l s='File size:' mod='touchize'}
    {$maxUploadSize|escape:'htmlall':'UTF-8'}&nbsp;{l s='kB max.' mod='touchize'}
</div>

{if $hasObject}

<div class="slq-touchmap-image-container" id="slq-touchmap-image-container">
    <img alt="" id="slq-touchmap-image" src="{$imgURL|escape:'htmlall':'UTF-8'}"/>
</div>
<canvas class="slq-touchmap-canvas" id="slq-touchmap-canvas">
</canvas>
<p class="alert alert-info">
    {l s='To make the banner interactive, draw an active area in the image by clicking, holding and drawing a rectangle with the mouse. Don’t forget that you can have many active areas on the same banner!' mod='touchize'}
    <br>
        {l s='Connect the active area to a product, search term or category using the boxes below the image.' mod='touchize'}
        <br>
            <strong>
                {l s='Note.' mod='touchize'}&nbsp;
            </strong>
            {l s='Please do not let areas overlap, this will lead to unwanted behaviour and confused customers.' mod='touchize'}
        </br>
    </br>
</p>
<table id="slq-action-area-list">
    <tbody id="slq-action-area-list-table">
    </tbody>
</table>
<script type="text/javascript">

    window.onload = TouchMap.Edit({
        "Debug": true, 
        "id": {$objectId|escape:'htmlall':'UTF-8'},
        "list": "?ajax=1&controller=AdminTouchmaps&action=getActionAreas&token={$token|escape:'htmlall':'UTF-8'}",
        "add": "?ajax=1&controller=AdminTouchmaps&action=addActionArea&token={$token|escape:'htmlall':'UTF-8'}",
        "edit": "?ajax=1&controller=AdminTouchmaps&action=updateActionArea&token={$token|escape:'htmlall':'UTF-8'}",
        "delete": "?ajax=1&controller=AdminTouchmaps&action=deleteActionArea&token={$token|escape:'htmlall':'UTF-8'}",
        "categories": "?ajax=1&controller=AdminTouchmaps&action=getCategories&token={$token|escape:'htmlall':'UTF-8'}",
        "products": "?ajax=1&controller=AdminTouchmaps&action=getProducts&token={$token|escape:'htmlall':'UTF-8'}",
        "Model": {
            "Id": {$objectId|escape:'htmlall':'UTF-8'},
            "Name": "{$objectName|escape:'htmlall':'UTF-8'}",
            "ImagePath": "{$imgURL|escape:'htmlall':'UTF-8'}",
            "ImageUrl": "{$imgURL|escape:'htmlall':'UTF-8'}",
            "Visibility": 1
        }
    });
    $("#image").change(function() {
        var fr = new FileReader;
        fr.onload = function() {
            var img = new Image;
            img.onload = function() {
                $("#width").val(img.width);
                $("#height").val(img.height);
                $("#slq-touchmap-image").attr("src", img.src);
            };
            img.src = fr.result;
        };
        fr.readAsDataURL(this.files[0]);
    });
</script>

{/if}

<script type="text/javascript">
    $(function() {
        var bannerImage = document.querySelector("#slq-touchmap-image");
        if (bannerImage) {
            if (bannerImage.complete) {
                var imageWidth = document.querySelector("#slq-touchmap-image").naturalWidth;
                var imageHeight = document.querySelector("#slq-touchmap-image").naturalHeight;
                $("#width").val(imageWidth);
                $("#height").val(imageHeight);
            } else {
                bannerImage.addEventListener("load", function() {
                    var imageWidth = this.naturalWidth;
                    var imageHeight = this.naturalHeight;
                    $("#width").val(imageWidth);
                    $("#height").val(imageHeight);
                });
            }
        }
        $("#image").change(function() {
            $("#loadScreen").show();
            var filename = $(this).val().split("\\").pop().split(".").shift();
            if ($("#name").val() == "") $("#name").val(filename);
            $("[name=submitAddtouchize_touchmapAndStay]").click();
        });
    });
</script>
