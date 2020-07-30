{if isset($posts) AND !empty($posts)}
<div id="recent_article_smart_blog_block_left"  class="vertical_mode block">
   <h4 class="title_block title_font"><a class="title_text" href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Recent Posts' mod='smartblogrecentposts'}</a></h4>
   <div class="block_content sdsbox-content">
      <ul class="recentArticles">
        {foreach from=$posts item="post"}
             {assign var="options" value=null}
             {$options.id_post= $post.id_smart_blog_post}
             {$options.slug= $post.link_rewrite}
             <li>
                 <a class="image" title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">
                     <img alt="{$post.meta_title}" src="{$link->getMediaLink($smarty.const._MODULE_DIR_)}/smartblog/images/{$post.post_img}-home-small.jpg">
                 </a>
                 <div class="right-block-smart">
                 <a class="title"  title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.meta_title}</a>
               <span class="info">{$post.created|date_format:"%b %d, %Y"}</span>
               	</div>
             </li>
         {/foreach}
            </ul>
   </div>
</div>
{/if}