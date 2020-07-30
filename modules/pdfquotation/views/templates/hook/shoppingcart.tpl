
{**
* ShoppingCart Template
* 
* @author Empty
* @copyright  Empty
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<br />

<div id="print-quotation" class="card">
    <div class="card-block">
        <h1 class="h1">{l s='Print a quote' mod='pdfquotation'}</h1>
    </div>

    <hr>

    <div id="customer-information">
        <form>
            <section class="form-fields">
                <div class="form-group row">
                    <label class="col-md-3 form-control-label">{l s='First Name' mod='pdfquotation'}</label>
                    <div class="col-md-6">
                        <input class="form-control" name="first_name" id="first_name" type="text" required="required">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 form-control-label">{l s='Last Name' mod='pdfquotation'}</label>
                    <div class="col-md-6">
                        <input class="form-control" name="last_name" id="last_name" type="text" required="required">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 form-control-label">{l s='E mail' mod='pdfquotation'}</label>
                    <div class="col-md-6">
                        <input class="form-control" name="email" id="email" type="email" required="required">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 form-control-label">{l s='Phone' mod='pdfquotation'}</label>
                    <div class="col-md-6">
                        <input class="form-control" name="phone" id="phone" type="text">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 form-control-label">{l s='To be contacted again' mod='pdfquotation'}</label>
                    <div class="col-md-6 form-control-valign">
                        <label class="radio-inline">
                            <span class="custom-radio">
                                <input type="radio" name="contacted" id="yes" value="1" checked="checked">
                                <span></span>
                            </span>
                            {l s='Yes' mod='pdfquotation'}
                        </label>

                        <label class="radio-inline">
                            <span class="custom-radio">
                                <input type="radio" name="contacted" id="no" value="0">
                                <span></span>
                            </span>
                            {l s='No' mod='pdfquotation'}
                        </label>
                    </div>
                </div>

                <input name="spam" id="spam" type="hidden" value=""/>
            </section>

            <footer class="form-footer clearfix">
                <button class="btn btn-primary form-control-submit pull-xs-right" type="submit">
                    {l s='Print' mod='pdfquotation'}
                </button>
            </footer>

            {*<div class="clearfix">*}

                {*<div class="radio-inline">*}
                    {*<label for="yes" class="top">*}
                        {*<div class="radio">*}
                            {*<span>*}

                            {*</span>*}
                        {*</div>*}
                        {*{l s='Yes' mod='pdfquotation'}*}
                    {*</label>*}
                {*</div>*}
                {*<div class="radio-inline">*}
                    {*<label for="no" class="top">*}
                        {*<div class="radio">*}
                            {*<span>*}
                                {*<input type="radio" name="contacted" id="no" value="0">*}
                            {*</span>*}
                        {*</div>*}
                        {*{l s='No' mod='pdfquotation'}*}
                    {*</label>*}
                {*</div>*}
            {*</div>*}

            {*<input name="spam" id="spam" type="hidden" value=""/>*}

            {*<button type="submit" class="button btn btn-default button-medium">*}
                {*<span>{l s='Print' mod='pdfquotation'}<i class="icon-chevron-right right"></i></span>*}
            {*</button>*}
        </form>
    </div>
</div>
