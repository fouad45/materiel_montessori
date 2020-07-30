{capture name=path}<a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='All Blog News' mod='smartblog'}</a>
     {if $title_category != ''}
    <span class="navigation-pipe">{$navigationPipe}</span>{$title_category}{/if}{/capture}
 
    {if $postcategory == ''}
        {include file="./search-not-found.tpl" postcategory=$postcategory}
    {else}
    <div id="smartblogcat" class="block">
{foreach from=$postcategory item=post}
<div itemtype="#" itemscope="" class="sdsarticleCat col-xs-12 col-sm-6">
    <div id="smartblogpost-{$post.id_post}">
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
                              <p class="sdstitle_block title_font"><a title="{$post.meta_title}" href='{smartblog::GetSmartBlogLink('smartblog_post',$options)}'>{$post.meta_title}</a></p>
         <span>
	     <span itemprop="articleSection"><i class="fa fa-tags"></i><a href="{smartblog::GetSmartBlogLink('smartblog_category',$catlink)}">{if $title_category != ''}{$title_category}{else}{$post.cat_name}{/if}</a></span>
	     <span class="comment"><i class="fa fa-comments"></i><a title="{$post.totalcomment} Comments" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{$post.totalcomment} {l s=' Comments' mod='smartblog'}</a></span>
	     {if $smartshowviewed ==1}<span class="right_blog_view"><i class="fa fa-eye-open"></i>{l s=' views' mod='smartblog'} ({$post.viewed})</span>{/if}
	     <span itemprop="author">{l s='Author' mod='smartblog'}:&nbsp;{if $smartshowauthor ==1}{if $smartshowauthorstyle != 0}{$post.firstname} {$post.lastname}{else}{$post.lastname} {$post.firstname}{/if}</span>{/if}
	 </span>
         {assign var="options" value=null}
                            {$options.id_post = $post.id_post} 
                            {$options.slug = $post.link_rewrite}
                          
    </div>
           <div class="sdsarticle-des">
          <span itemprop="description" class="clearfix"><div id="lipsum">
	{$post.short_description}</div></span>
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
 {/if}
 {if isset($smartcustomcss)}
    <style>
        {$smartcustomcss}
    </style>
{/if}

