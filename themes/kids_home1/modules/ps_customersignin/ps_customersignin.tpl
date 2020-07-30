<div class="mobile_links-wrapper">
<div id="mobile_links" class="dropdown js-dropdown hidden-md-up">
    <span class="expand-more" data-toggle="dropdown"><i class="fa fa-user"></i>{l s='My account'}<i class="fa fa-caret-down"></i></span>
    <ul class="dropdown-menu">
    <li>
    <a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='My Account'}" rel="nofollow"><i class="fa fa-user"></i>  {l s='My Account'}</a></li>
     <li>
    <a href="{$link->getPageLink('contact', true)|escape:'html'}" title="{l s='Contact us'}" rel="nofollow"><i class="fa fa-heart"></i>  {l s='Contact us'}</a></li>
    <li>
        <a class="link-myaccount" href="{$link->getPageLink('cart', true)|escape:'html'}?action=show" title="{l s='My Cart'}" rel="nofollow">
        <i class="fa fa-shopping-cart"></i>{l s='My Cart'}
        </a>
    </li>
    {if $logged}
      <li>
      <a
        href="{$my_account_url}"
        title="{l s='View my customer account' d='Shop.Theme.CustomerAccount'}"
        rel="nofollow"
      >
        <i class="fa fa-wrench"></i>
        <span>{$customerName}</span>
      </a>
      </li>
      <li>
      <a
        href="{$logout_url}"
        rel="nofollow"
      >
        <i class="fa fa-sign-out"></i>
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
      </li>
    {else}
    <li>
      <a
        href="{$my_account_url}"
        title="{l s='Log in to your customer account' d='Shop.Theme.CustomerAccount'}"
        rel="nofollow"
      >
        <i class="fa fa-user"></i>
        <span>{l s='Sign in' d='Shop.Theme.Actions'}</span>
      </a>
      </li>
    {/if}
</ul>
</div>
</div>
<div id="header_links" class="hidden-sm-down">
<ul>
    <li>
    <a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='My Account'}" rel="nofollow"><i class="fa fa-user"></i>  {l s='My Account'}</a></li>
    <li>
        <a href="{$link->getPageLink('', true)|escape:'html'}contact-us" title="{l s='Contact us'}" rel="nofollow"><i class="fa fa-user"></i>  {l s='Contact us'}</a></li>
    <li>
        <a href="{$link->getPageLink('cart', true)|escape:'html'}?action=show" title="{l s='My Cart'}" rel="nofollow">
        <i class="fa fa-shopping-cart"></i>{l s='My Cart'}
        </a>
    </li>
    {if $logged}
      <li>
      <a
        class="account"
        href="{$my_account_url}"
        title="{l s='View my customer account' d='Shop.Theme.CustomerAccount'}"
        rel="nofollow"
      >
        <i class="fa fa-wrench"></i>
        <span>{$customerName}</span>
      </a>
      </li>
      <li>
      <a
        class="logout"
        href="{$logout_url}"
        rel="nofollow"
      >
        <i class="fa fa-sign-out"></i>
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
      </li>
    {else}
    <li>
      <a
        href="{$my_account_url}"
        title="{l s='Log in to your customer account' d='Shop.Theme.CustomerAccount'}"
        rel="nofollow"
      >
        <i class="fa fa-user"></i>
        <span>{l s='Sign in' d='Shop.Theme.Actions'}</span>
      </a>
      </li>
    {/if}
</ul>
</div>
