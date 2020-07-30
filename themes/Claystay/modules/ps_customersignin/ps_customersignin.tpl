<div id="_desktop_user_info">
	<div class="pst_userinfotitle">
  		<i class="fa fa-user-o hidden-lg-up"></i>
		<span class="user-info-icon">{l s='My Account' d='Shop.Theme.CustomerAccount'}</span>
	</div>
  	<div class="user-info">
    	{if $logged}
	  		<a
				class="account"
				href="{$my_account_url}"
				title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
				rel="nofollow"
		 	>
				<i class="fa fa-user" aria-hidden="true"></i>
				<span class="user-name">{$customerName}</span>
      		</a>
			
			
			  <a
        class="ap-btn-compare dropdown-item"
        href="{url entity='module' name='stfeature' controller='productscompare'}"
        title="{l s='Compare' d='Shop.Theme.Global'}"
        rel="nofollow"
      >
      <i class="fa fa-compress" aria-hidden="true"></i>
	  <span>{l s='Compare' d='Shop.Theme.Global'}</span>
 
     </a>
	   <a
      class="ap-btn-wishlist user-info-icon"
      href="{url entity='module' name='stfeature' controller='mywishlist'}"
      title="{l s='Wishlist' d='Shop.Theme.Global'}"
      rel="nofollow"
    >
      <i class="fa fa-heart" aria-hidden="true"></i>
	  <span>{l s='Wishlist' d='Shop.Theme.Global'}</span>
 
    </a>
			
			
			<a
        		class="logout"
        		href="{$logout_url}"
        		rel="nofollow"
      		>
        		<i class="fa fa-sign-out" aria-hidden="true"></i>
        		{l s='Sign out' d='Shop.Theme.Actions'}
      		</a>      
    	{else}
     	 	<a
        		href="{$my_account_url}"
        		title="{l s='Log in to your customer account' d='Shop.Theme.CustomerAccount'}"
        		rel="nofollow"
      		>
        		<i class="fa fa-lock" aria-hidden="true"></i>
        		<span class="sign-in">{l s='Sign in' d='Shop.Theme.Actions'}</span>
      		</a>
			
			
			  <a
        class="ap-btn-compare dropdown-item"
        href="{url entity='module' name='stfeature' controller='productscompare'}"
        title="{l s='Compare' d='Shop.Theme.Global'}"
        rel="nofollow"
      >
      <i class="fa fa-compress" aria-hidden="true"></i>
	  <span>{l s='Compare' d='Shop.Theme.Global'}</span>
 
     </a>
	   <a
      class="ap-btn-wishlist user-info-icon"
      href="{url entity='module' name='stfeature' controller='mywishlist'}"
      title="{l s='Wishlist' d='Shop.Theme.Global'}"
      rel="nofollow"
    >
      <i class="fa fa-heart" aria-hidden="true"></i>
	  <span>{l s='Wishlist' d='Shop.Theme.Global'}</span>
 
    </a>
    	{/if}
    
  </div>
</div>