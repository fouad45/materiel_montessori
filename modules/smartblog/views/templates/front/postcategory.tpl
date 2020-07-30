{extends file=$layout_category}
{block name='head_seo'}
  <title> {if $title_category != ''}{$title_category}{else}{l s='All Blog News' mod='smartblog'}{/if}</title>
{/block}
{block name='breadcrumb'}
{if isset($breadcrumb)}
<nav class="breadcrumb">
{hook h="blockPosition8"}
  <ol itemscope itemtype="http://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="{$breadcrumb.links[0].url}">
          <span itemprop="name">{$breadcrumb.links[0].title}</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="{smartblog::GetSmartBlogLink('smartblog')}">
        <span itemprop="name">{l s='All Blog News' mod='smartblog'}</span>
        </a>
      </li>
      {if $title_category != ''}
      {assign var="link_category" value=null}
    {$link_category.id_category = $id_category}
    {$link_category.slug = $cat_link_rewrite}
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="{smartblog::GetSmartBlogLink('smartblog_category',$link_category)}">
        <span itemprop="name">{$title_category}</span>
        </a>
      </li>
    {/if}
  </ol>
</nav>
{/if}
{/block}
{block name='content'}
    {if $postcategory == ''}
        {if $title_category != ''}
             <p class="error">{l s='No Post in Category' mod='smartblog'}</p>
        {else}
             <p class="error">{l s='No Post in Blog' mod='smartblog'}</p>
        {/if}
    {else}
	{if $smartdisablecatimg == '1'}
                  {assign var="activeimgincat" value='0'}
                    {$activeimgincat = $smartshownoimg} 
        {if $title_category != ''}        
           {foreach from=$categoryinfo item=category}
            <div class="sdsblogCategory">
               {if ($cat_image != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                   <img alt="{$category.meta_title}" src="{$link->getMediaLink($smarty.const._MODULE_DIR_)}/smartblog/images/category/{$cat_image}-home-default.jpg" class="imageFeatured">
               {/if}
                {$category.description}
            </div>
             {/foreach}  
        {/if}
    {/if}
    <div class="smartblogcat" class="clearfix">
	<div class="row">
{foreach from=$postcategory item=post}
        <div itemtype="#" itemscope="" class="sdsarticleCat col-xs-12 col-sm-6">
            <div class="smartblogpost-{$post.id_post}">
            <div class="articleContent">
                  <a itemprop="url" title="{$post.meta_title}" class="imageFeaturedLink">
                            {assign var="activeimgincat" value='0'}
                            {$activeimgincat = $smartshownoimg} 
                            {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                                              {assign var="options" value=null}
                                {$options.id_post = $post.id_post}  
                                {$options.slug = $post.link_rewrite}  
                                 <a title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" class="r_more">              <img itemprop="image" alt="{$post.meta_title}" src="{$link->getMediaLink($smarty.const._MODULE_DIR_)}/smartblog/images/{$post.post_img}-single-default.jpg" class="imageFeatured"></a>
                            {/if}
                  </a>
            </div>
            <div class="right_blog_cat">
                
                <div class="sdsarticleHeader">
                    {assign var="options" value=null}
                                {$options.id_post = $post.id_post}
                                {$options.slug = $post.link_rewrite}
                       {assign var="catlink" value=null}
                                    {$catlink.id_category = $post.id_category}
                                    {$catlink.slug = $post.cat_link_rewrite}
                                      <div class="sdstitle_block title_font"><a title="{$post.meta_title}" href='{smartblog::GetSmartBlogLink('smartblog_post',$options)}'>{$post.meta_title}</a></div>
                 <div class="info-category">
                 <span itemprop="articleSection"><i class="fa fa-tags"></i><a href="{smartblog::GetSmartBlogLink('smartblog_category',$catlink)}">{if $title_category != ''}{$title_category}{else}{$post.cat_name}{/if}</a></span>
                 <span class="comment"><i class="fa fa-comments"></i><a title="{$post.totalcomment} Comments" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{$post.totalcomment} {l s=' Comments' mod='smartblog'}</a></span>
                 {if $smartshowviewed ==1}<span class="right_blog_view"><i class="fa fa-eye"></i>{l s=' views' mod='smartblog'} ({$post.viewed})</span>{/if}
                 <span itemprop="author">{l s='Author' mod='smartblog'}:&nbsp;{if $smartshowauthor ==1}{if $smartshowauthorstyle != 0}{$post.firstname} {$post.lastname}{else}{$post.lastname} {$post.firstname}{/if}</span>{/if}
             </div>
                 {assign var="options" value=null}
                {$options.id_post = $post.id_post} 
                {$options.slug = $post.link_rewrite}
                                  
            </div>
                   <div class="sdsarticle-des">
                  <span itemprop="description" class="clearfix"><div class="lipsum">
            {$post.short_description nofilter}</div></span>
                 </div>
                <div class="sdsreadMore">
                          {assign var="options" value=null}
                                {$options.id_post = $post.id_post}  
                                {$options.slug = $post.link_rewrite}  
        {*                         <span class="more"><a title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" class="r_more">{l s='Read more' mod='smartblog'} </a></span>*}
                </div>
                </div>
           </div>
        </div>
{/foreach}
	</div>
    </div>
    {if !empty($pagenums)}
<nav class="pagination">
<div class="col-md-4">{l s='Showing' mod='smartblog'} {if $limit_start!=0}{$limit_start}{else}1{/if} {l s='to' mod='smartlatestnews'} {if $limit_start+$limit >= $total}{$total}{else}{$limit_start+$limit}{/if} {l s='of' mod='smartblog'} {$total} ({$c} {l s='Pages' mod='smartblog'})
</div>
<div class="col-md-8">
<ul class="page-list clearfix text-xs-center">
{assign var="options" value=null}
{if $c > 1}
{$options.page = $c-1}
{else}
{$options.page =1}
{/if}
<li><a class="previous {if $c > 1}{else}disabled{/if}" {if $c > 1}href="{smartblog::GetSmartBlogLink('smartblog_list_pagination',$options)}"{else} href="javascript:void(0)"{/if}><i class="fa fa-angle-double-left"></i>{l s='Previous' d='Shop.Theme.Actions'}</a></li>
{for $k=0 to $pagenums}
    {if $title_category != ''}
        {assign var="options" value=null}
        {$options.page = $k+1}
        {$options.id_category = $id_category}
        {$options.slug = $cat_link_rewrite}
    {else}
        {assign var="options" value=null}
        {$options.page = $k+1}
    {/if}
    {if ($k+1) == $c}
        <li class="current"><a href="{smartblog::GetSmartBlogLink('smartblog_list_pagination',$options)}">{$k+1}</a></li>
    {else}
            {if $title_category != ''}
                <li><a href="{smartblog::GetSmartBlogLink('smartblog_category_pagination',$options)}">{$k+1}</a></li>
            {else}
                <li><a href="{smartblog::GetSmartBlogLink('smartblog_list_pagination',$options)}">{$k+1}</a></li>
            {/if}
    {/if}
{/for}
{assign var="options" value=null}
{if $c < ($pagenums+1)}
{$options.page = $c+1}
{else}
{$options.page =$pagenums+1}
{/if}
<li><a class="next {if $c < ($pagenums+1)}{else}disabled{/if}" {if $c < ($pagenums+1)} href="{smartblog::GetSmartBlogLink('smartblog_list_pagination',$options)}"{else} href="javascript:void(0)"{/if}>{l s='Next' d='Shop.Theme.Actions'}<i class="fa fa-angle-double-right"></i></a></li>
</ul>
</div>
</nav>
{/if}
 {/if}
{if isset($smartcustomcss)}
    <style>
        {$smartcustomcss}
    </style>
{/if}
{/block}